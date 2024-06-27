<?php
require_once('../config/database.php');
require_once('../classes/searchResult.php');

/**
 * Class searchRepository
 * This class handles all database operations related to searching for artists and artworks.
 */
class searchRepository
{
    /** @var Database $database Database connection object */
    private $database;

    /**
     * Constructor for searchRepository class.
     * @param Database $db The database connection object.
     */
    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * Searches for artists based on provided criteria.
     * @param array $data Search criteria data.
     * @param string $sort Sorting field.
     * @param string $order Sorting order (ASC or DESC).
     * @param string $secondarySort Secondary sorting field.
     * @return array Array of SearchResult objects representing artists.
     * @throws Exception If an error occurs during the database operation.
     */
    public function searchArtists($data, $sort, $order, $secondarySort)
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sortField = $sort === 'yearOfBirth' ? 'yearOfBirth' : "CASE WHEN firstName IS NULL OR firstName = '' THEN lastName ELSE firstName END";
        $secondarySort = $sort === 'yearOfBirth' ? 'firstName' : 'lastName';

        $sql = "SELECT DISTINCT(artistID), 
                       CASE 
                           WHEN firstName IS NULL AND lastName IS NULL THEN 'Unknown'
                           WHEN firstName IS NULL THEN lastName
                           WHEN lastName IS NULL THEN firstName
                           ELSE CONCAT(firstName, ' ', lastName) 
                       END AS artistName, 
                       yearOfBirth, yearOfDeath, nationality 
                FROM artists
                WHERE 1=1";

        if (!empty($data['artistName'])) {
            // Split the input into first name and last name parts
            $names = explode(' ', $data['artistName']);
            $firstName = isset($names[0]) ? $names[0] : '';
            $lastName = isset($names[1]) ? $names[1] : '';

            $sql .= " AND (
                    (firstName LIKE :artistName OR lastName LIKE :artistName) OR 
                    (firstName LIKE :firstName AND lastName LIKE :lastName) OR
                    (SOUNDEX(firstName) = SOUNDEX(:firstName) AND SOUNDEX(lastName) = SOUNDEX(:lastName))
                )";
        }
        if (!empty($data['yearOfBirth'])) {
            $sql .= " AND yearOfBirth >= :yearOfBirth";
        }
        if (!empty($data['yearOfDeath'])) {
            $sql .= " AND yearOfDeath <= :yearOfDeath";
        }
        if (!empty($data['nationality'])) {
            $sql .= " AND (nationality LIKE :nationality OR SOUNDEX(nationality) = SOUNDEX(:nationality))";
        }

        $sql .= " ORDER BY $sortField $order, $secondarySort $order";
        $stmt = $this->database->prepareStatement($sql);

        if (!empty($data['artistName'])) {
            $stmt->bindValue(':artistName', '%' . $data['artistName'] . '%', PDO::PARAM_STR);
            $stmt->bindValue(':firstName', '%' . $firstName . '%', PDO::PARAM_STR);
            $stmt->bindValue(':lastName', '%' . $lastName . '%', PDO::PARAM_STR);
        }
        if (!empty($data['yearOfBirth'])) {
            $stmt->bindValue(':yearOfBirth', $data['yearOfBirth'], PDO::PARAM_INT);
        }
        if (!empty($data['yearOfDeath'])) {
            $stmt->bindValue(':yearOfDeath', $data['yearOfDeath'], PDO::PARAM_INT);
        }
        if (!empty($data['nationality'])) {
            $stmt->bindValue(':nationality', '%' . $data['nationality'] . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        $results = $stmt->fetchAll();

        $searchResults = [];
        foreach ($results as $result) {
            $searchResults[] = new SearchResult($result);
        }

        return $searchResults;
    }

    /**
     * Searches for artworks based on provided criteria.
     * @param array $data Search criteria data.
     * @param string $sort Sorting field.
     * @param string $order Sorting order (ASC or DESC).
     * @param string $secondarySort Secondary sorting field.
     * @return array Array of SearchResult objects representing artworks.
     * @throws Exception If an error occurs during the database operation.
     */
    public function searchArtworks($data, $sort, $order, $secondarySort)
    {
        $sql = "SELECT DISTINCT(a.artworkID), a.title,  
                       CASE 
                           WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Unknown'
                           WHEN b.firstName IS NULL THEN b.lastName
                           WHEN b.lastName IS NULL THEN b.firstName
                           ELSE CONCAT(b.firstName, ' ', b.lastName) 
                       END AS artistName,
                       a.yearOfWork, a.imagefilename, a.artistID
                FROM artworks a
                JOIN artists b ON a.artistID = b.artistID
                LEFT JOIN artworkgenres ag ON a.artworkID = ag.artworkID
                LEFT JOIN genres ge ON ag.genreID = ge.genreID
                WHERE 1=1";

        if (!empty($data['artworkTitle'])) {
            $sql .= " AND (a.title LIKE :artworkTitle OR SOUNDEX(a.title) = SOUNDEX(:artworkTitle))";
        }
        if (!empty($data['yearOfWorkStart'])) {
            $sql .= " AND a.yearOfWork >= :yearOfWorkStart";
        }
        if (!empty($data['yearOfWorkEnd'])) {
            $sql .= " AND a.yearOfWork <= :yearOfWorkEnd";
        }
        if (!empty($data['genre'])) {
            $sql .= " AND (ge.genreName LIKE :genre OR SOUNDEX(ge.genreName) = SOUNDEX(:genre))";
        }

        $sql .= " ORDER BY " . ($sort === 'artistName' ? "artistName" : "a.$sort") . " $order, $secondarySort $order";
        $stmt = $this->database->prepareStatement($sql);

        if (!empty($data['artworkTitle'])) {
            $stmt->bindValue(':artworkTitle', '%' . $data['artworkTitle'] . '%', PDO::PARAM_STR);
        }
        if (!empty($data['yearOfWorkStart'])) {
            $stmt->bindValue(':yearOfWorkStart', $data['yearOfWorkStart'], PDO::PARAM_INT);
        }
        if (!empty($data['yearOfWorkEnd'])) {
            $stmt->bindValue(':yearOfWorkEnd', $data['yearOfWorkEnd'], PDO::PARAM_INT);
        }
        if (!empty($data['genre'])) {
            $stmt->bindValue(':genre', '%' . $data['genre'] . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        $results = $stmt->fetchAll();

        $searchResults = [];
        foreach ($results as $result) {
            $searchResults[] = new SearchResult($result);
        }

        return $searchResults;
    }

    /**
     * Performs a simple search based on a search term.
     * @param string $searchTerm The search term.
     * @param string $sort Sorting field.
     * @param string $order Sorting order (ASC or DESC).
     * @param string $secondarySort Secondary sorting field.
     * @return array Array of SearchResult objects representing artworks and artists.
     * @throws Exception If an error occurs during the database operation.
     */
    public function simpleSearch($searchTerm, $sort, $order, $secondarySort)
    {
        // SQL query construction based on search criteria trims Spaces
        if (strpos($searchTerm, ' ') !== false) {
            list($firstName, $lastName) = explode(' ', $searchTerm, 2);
            $firstName = '%' . $firstName . '%';
            $lastName = '%' . $lastName . '%';

            $sql = "SELECT DISTINCT(a.artworkID), a.title, 
                       CASE 
                           WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Unknown'
                           WHEN b.firstName IS NULL THEN b.lastName
                           WHEN b.lastName IS NULL THEN b.firstName
                           ELSE CONCAT(b.firstName, ' ', b.lastName) 
                       END AS artistName,
                       a.yearOfWork, a.imagefilename, a.artistID
                FROM artworks a
                JOIN artists b ON a.artistID = b.artistID
                WHERE (b.firstName LIKE :firstName OR b.lastName LIKE :lastName)
                   OR a.title LIKE :searchTerm 
                   OR SOUNDEX(a.title) = SOUNDEX(:searchTerm)
                   OR SOUNDEX(b.firstName) = SOUNDEX(:firstName)
                   OR SOUNDEX(b.lastName) = SOUNDEX(:lastName)";
        } else {

            $searchTerm = '%' . $searchTerm . '%';
            $sql = "SELECT a.artworkID, a.title, 
                       CASE 
                           WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Unknown'
                           WHEN b.firstName IS NULL THEN b.lastName
                           WHEN b.lastName IS NULL THEN b.firstName
                           ELSE CONCAT(b.firstName, ' ', b.lastName) 
                       END AS artistName,
                       a.yearOfWork, a.imagefilename, a.artistID    
                FROM artworks a
                JOIN artists b ON a.artistID = b.artistID
                WHERE (b.firstName LIKE :searchTerm OR b.lastName LIKE :searchTerm)
                   OR a.title LIKE :searchTerm 
                   OR SOUNDEX(a.title) = SOUNDEX(:searchTerm)
                   OR SOUNDEX(b.firstName) = SOUNDEX(:searchTerm)
                   OR SOUNDEX(b.lastName) = SOUNDEX(:searchTerm)";
        }
        $sql .= " ORDER BY " . ($sort === 'artistName' ? "artistName" : "a.$sort") . " $order, $secondarySort $order";
        $stmt = $this->database->prepareStatement($sql);

        // Binden des Suchbegriffs
        $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        if (isset($firstName)) {
            $stmt->bindValue(':firstName', $firstName, PDO::PARAM_STR);
        }
        if (isset($lastName)) {
            $stmt->bindValue(':lastName', $lastName, PDO::PARAM_STR);
        }

        $stmt->execute();
        $results = $stmt->fetchAll();

        $searchResults = [];
        foreach ($results as $result) {
            $searchResults[] = new SearchResult($result);
        }

        return $searchResults;
    }

    /**
     * Retrieves favorite artwork details by its ID.
     * @param int $artworkID The artwork ID.
     * @return SearchResult|null SearchResult object if found, otherwise null.
     * @throws Exception If an error occurs during the database operation.
     */
    public function getFavoriteArtworkByID($artworkID)
    {
        $sql = "
        SELECT 
            a.title,
            a.artistID, 
            a.artworkID, 
            a.yearOfWork, 
            CONCAT_WS(' ', COALESCE(b.firstName, ''), COALESCE(b.lastName, 'Unknown')) AS artistName,
            a.imagefilename 
        FROM 
            artworks a
        JOIN 
            artists b ON a.artistID = b.artistID
        WHERE 
            a.artworkID = :artworkID
        GROUP BY 
            a.artworkID";

        try {
            if (!$this->database->isConnected()) {
                $this->database->connect();
            }

            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return new SearchResult($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching artwork details: " . $e->getMessage());
        } finally {
            $this->database->close();
        }
    }

    /**
     * Retrieves favorite artist details by their ID.
     * @param int $artistID The artist ID.
     * @return SearchResult|null SearchResult object if found, otherwise null.
     * @throws Exception If an error occurs during the database operation.
     */
    public function getFavoriteArtistByID($artistID)
    {
        $sql = "
        SELECT 
            artistID,  
            nationality, 
            yearOfBirth, 
            yearOfDeath,
            CONCAT_WS(' ', COALESCE(firstName, ''), COALESCE(lastName, 'Unknown')) AS artistName
        FROM 
            artists 
        WHERE 
            artistID = :artistID";

        try {
            if (!$this->database->isConnected()) {
                $this->database->connect();
            }

            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artistID', $artistID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return new SearchResult($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception("Error fetching artist details: " . $e->getMessage());
        } finally {
            $this->database->close();
        }
    }


}

?>
