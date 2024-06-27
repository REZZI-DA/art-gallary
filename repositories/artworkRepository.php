<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../classes/Artwork.php');

class artworkRepository
{
    private $database;

    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * Get artwork details by artwork ID
     * @param int $artworkID
     * @return Artwork|null
     * @throws Exception
     */
    public function getArtworkByID($artworkID)
    {
        $sql = "
    SELECT 
        a.artistID, 
        a.artworkID, 
        a.title, 
        a.yearOfWork, 
        a.medium, 
        a.width, 
        a.height, 
        a.description,
        CONCAT_WS(' ', COALESCE(b.firstName, ''), COALESCE(b.lastName, 'Unknown')) AS artistName,
        CONCAT_WS(', ', g.galleryName, g.galleryCity) AS galleryInfo,
        g.latitude, 
        g.longitude,
        a.imagefilename 
    FROM 
        artworks a
    JOIN 
        artists b ON a.artistID = b.artistID
    LEFT JOIN 
        galleries g ON a.galleryID = g.galleryID
    WHERE 
        a.artworkID = :artworkID
    GROUP BY 
        a.artworkID";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $genres = $this->getGenresForArtwork($artworkID);
                $subjects = $this->getSubjectsForArtwork($artworkID);

                $result['genreNames'] = $genres['genreNames'];
                $result['genreIDs'] = $genres['genreIDs'];
                $result['subjectNames'] = $subjects['subjectNames'];
                $result['subjectIDs'] = $subjects['subjectIDs'];

                return new Artwork($result);
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
     * Get genres for a specific artwork by artwork ID
     * @param int $artworkID
     * @return array
     * @throws Exception
     */
    public function getGenresForArtwork($artworkID)
    {
        $sql = "
    SELECT 
        GROUP_CONCAT(DISTINCT ge.genreName ORDER BY ge.genreName SEPARATOR ';') AS genreNames,
        GROUP_CONCAT(DISTINCT ge.genreID ORDER BY ge.genreName SEPARATOR ';') AS genreIDs
    FROM 
        artworkgenres ag
    JOIN 
        genres ge ON ag.genreID = ge.genreID
    WHERE 
        ag.artworkID = :artworkID";

        try {
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching genres: " . $e->getMessage());
        }
    }

    /**
     * Get subjects for a specific artwork by artwork ID
     * @param int $artworkID
     * @return array
     * @throws Exception
     */
    public function getSubjectsForArtwork($artworkID)
    {
        $sql = "
    SELECT 
        GROUP_CONCAT(DISTINCT su.subjectName ORDER BY su.subjectName SEPARATOR ';') AS subjectNames,
        GROUP_CONCAT(DISTINCT su.subjectID ORDER BY su.subjectName SEPARATOR ';') AS subjectIDs
    FROM 
        artworksubjects asu
    JOIN 
        subjects su ON su.subjectID = asu.subjectID
    WHERE 
        asu.artworkID = :artworkID";

        try {
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching subjects: " . $e->getMessage());
        }
    }

    /**
     * Get artworks by artist ID
     * @param int $artistID
     * @return Artwork[]
     * @throws Exception
     */
    public function getArtworksByArtistID($artistID)
    {
        $sql = "SELECT artworkID, title, imagefilename
        FROM artworks 
        WHERE artistID = :artistID";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artistID', $artistID, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $this->database->close();

            $artworks = [];
            foreach ($results as $result) {
                $artworks[] = new Artwork($result);
            }
            return $artworks;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artworks by artist ID: " . $e->getMessage());
        }
    }

    /**
     * Get all artworks with sorting options
     * @param string $sort
     * @param string $order
     * @param string $secondarySort
     * @return Artwork[]
     * @throws Exception
     */
    public function getAllArtworks($sort, $order, $secondarySort)
    {
        $sql = "SELECT a.artworkID, a.title, 
               CASE 
                   WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Artist is Unknown'
                   WHEN b.firstName IS NULL THEN b.lastName
                   WHEN b.lastName IS NULL THEN b.firstName
                   ELSE CONCAT(b.firstName, ' ', b.lastName) 
               END AS artistName,
               a.yearOfWork, a.imagefilename, a.artistID
        FROM artworks a
        JOIN artists b ON a.artistID = b.artistID
        ORDER BY " . ($sort === 'artistName' ? "artistName" : "a.$sort") . " $order, $secondarySort $order";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $this->database->close();

            $artworks = [];
            foreach ($results as $result) {
                $artworks[] = new Artwork($result);
            }
            return $artworks;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artworks: " . $e->getMessage());
        }
    }

    /**
     * Get three random artworks based on provided image names
     * @param array $imageNames
     * @return Artwork[]
     * @throws Exception
     */
    public function getThreeRandomArtworks($imageNames)
    {


        foreach ($imageNames as $index => $imageName) {
            // Überprüfe, ob das erste Zeichen '0' ist
            if ($imageName[0] === '0') {
                // Überprüfe, ob das zweite Zeichen '9' ist
                if ($imageName[1] != '9') {
                    // Entferne das zweite Zeichen
                    $imageNames[$index] = substr($imageName, 1);
                }
            }
        }
        $imageNameString = "'" . implode("','", $imageNames) . "'";
        $sql = "SELECT 
        a.artworkID, 
        a.artistID, 
        a.imagefilename, 
        a.title, 
        a.description,
        CASE 
            WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Artist is Unknown'
            WHEN b.firstName IS NULL THEN b.lastName
            WHEN b.lastName IS NULL THEN b.firstName
            ELSE CONCAT(b.firstName, ' ', b.lastName) 
        END AS artistName
    FROM artworks a
    JOIN artists b ON a.artistID = b.artistID
    WHERE a.description IS NOT NULL
    AND a.imagefilename IN ($imageNameString)
    ORDER BY RAND()
    LIMIT 3;";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->database->close();

            $artworks = [];
            foreach ($results as $result) {
                $artworks[] = new Artwork($result);
            }

            return $artworks;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artworks: " . $e->getMessage());
        }
    }

    /**
     * Get the three most reviewed artworks
     * @return array
     * @throws Exception
     */
    public function threemostreviewartworks()
    {

        $sql = "SELECT a.artworkID, a.imagefilename,a.title, a.originalHome,t.AverageRating
        FROM artworks a
        JOIN (
            SELECT artworkID, AVG(Rating) AS AverageRating
            FROM reviews
            GROUP BY artworkID
            HAVING COUNT(*) >= 3
            ORDER BY AVG(Rating) DESC
            LIMIT 3
        ) AS t ON a.artworkID = t.artworkID;";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->database->close();
            return $results;
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artworks: " . $e->getMessage());
        }
    }

    /**
     * Get artwork review data by artwork ID
     * @param int $artworkID
     * @return array
     * @throws Exception
     */
    public function getArtworkReview($artworkID)
    {
        $sql = "SELECT a.artworkID, a.imagefilename, a.title, 
                   COALESCE(AVG(r.Rating), 0) AS AverageRating,
                   COUNT(r.Rating) AS TotalReviews
            FROM artworks a
            LEFT JOIN reviews r ON a.artworkID = r.artworkID
            WHERE a.artworkID = :artworkID
            GROUP BY a.artworkID, a.imagefilename, a.title";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->database->close();

            if ($result) {
                return $result;
            } else {
                return ['artworkID' => $artworkID, 'imagefilename' => null, 'title' => null, 'AverageRating' => null, 'TotalReviews' => 0];
            }
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artwork review data: " . $e->getMessage());
        }
    }

}
