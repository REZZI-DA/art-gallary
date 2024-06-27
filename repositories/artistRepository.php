<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../classes/Artist.php');

class ArtistRepository
{
    private $database;

    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * Get artist details by artist ID
     * @param int $artistID
     * @return Artist|null
     * @throws Exception
     */
    public function getArtistByID($artistID)
    {
        $sql = "
        SELECT 
            artistID, 
            firstName, 
            lastName, 
            nationality, 
            yearOfBirth, 
            yearOfDeath, 
            details,
            artistLink
        FROM 
            artists 
        WHERE 
            artistID = :artistID";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artistID', $artistID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $this->database->close();

            if ($result) {
                return new Artist($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artist details: " . $e->getMessage());
        }
    }

    /**
     * Get all artists with sorting options
     * @param string $sort
     * @param string $order
     * @param string $secondarySort
     * @return Artist[]
     * @throws Exception
     */
    public function getAllArtists($sort, $order, $secondarySort)
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

        $sortField = $sort === 'yearOfBirth' ? 'yearOfBirth' : "CASE WHEN firstName IS NULL OR firstName = '' THEN lastName ELSE firstName END";
        $secondarySort = $sort === 'yearOfBirth' ? 'firstName' : 'lastName';

        $sql = "
    SELECT 
        artistID, 
        firstName, 
        lastName, 
        nationality, 
        yearOfBirth, 
        yearOfDeath, 
        details,
        artistLink
    FROM 
        artists 
    ORDER BY 
        $sortField $order, 
        $secondarySort $order";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $this->database->close();

            $artists = [];
            foreach ($results as $result) {
                $artists[] = new Artist($result);
            }
            return $artists;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artists: " . $e->getMessage());
        }
    }

    /**
     * Get the three most reviewed artists
     * @return Artist[]
     * @throws Exception
     */
    public function threeMostReviewedArtists()
    {
        $sql = "SELECT b.artistID,b.firstName,b.lastName, b.nationality, b.yearOfBirth,b.yearOfDeath,b.artistLink,
                COUNT(*) AS reviewCount
        FROM artworks a
        LEFT JOIN artists b ON a.artistID = b.artistID
        GROUP BY b.artistID
        ORDER BY reviewCount DESC
        LIMIT 3";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->database->close();
            $artists = [];
            foreach ($results as $result) {
                $artist = new Artist($result);
                $artist->setReviewCount($result['reviewCount']);
                $artists[] = $artist;
            }

            return $artists;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artists: " . $e->getMessage());
        }
    }
}