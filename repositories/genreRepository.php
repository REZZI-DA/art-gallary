<?php
require_once('../config/database.php');
require_once('../classes/genre.php');

class GenreRepository
{
    private $database;

    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * Get all genres
     * @return array
     * @throws Exception
     */
    public function getAllGenres()
    {
        $sql = "
        SELECT 
           g.GenreID,
           g.GenreName,
           g.Era
        FROM genres g
       
        ORDER BY g.Era, g.GenreName ";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $this->database->close();

            // if ($result) {
            return $result;
            /* } else {
                 return null;
             }*/
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artwork details: " . $e->getMessage());
        }
    }

    /**
     * Get number of artworks by genre ID
     * @param int $genreID
     * @return mixed
     * @throws Exception
     */
    public function getNumberOfArtworksByGenreID($genreID)
    {
        $sql = "SELECT Count(*)
        FROM artworks a
        JOIN artists b ON a.artistID = b.artistID
        JOIN artworkgenres g ON a.ArtWorkID = g.artworkID
        WHERE g.GenreID = :genreId";
        try {

            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':genreId', $genreID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $this->database->close();
            if ($result) {

                return $result;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artwork details: " . $e->getMessage());
        }
    }

    /**
     * Get artists of a specific genre by genre ID
     * @param int $genreID
     * @return array
     * @throws Exception
     */
    public function getArtistsOfGenre($genreID)
    {
        $sql = "
        SELECT  b.firstName, b.lastName
        FROM artworks a
        JOIN artists b ON a.artistID = b.artistID
        JOIN artworkgenres g ON a.ArtWorkID = g.artworkID
        WHERE g.GenreID = :genreId
        GROUP BY b.firstName, b.lastName
            ";
        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':genreId', $genreID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $this->database->close();
            if ($result) {

                return $result;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artwork details: " . $e->getMessage());
        }
    }

    /**
     * Get genre details by genre ID
     * @param int $genreId
     * @return mixed
     * @throws Exception
     */
    public function getGenreById($genreId)
    {
        $sql = "
      SELECT 
         g.GenreName,
           g.Era,
            g.Description,
            g.Link
        FROM genres g
        WHERE g.GenreID = :genreId
        ";

        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':genreId', $genreId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $this->database->close();

            if ($result) {

                return $result;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->database->close();
            throw new Exception("Error fetching artwork details: " . $e->getMessage());
        }
    }
}