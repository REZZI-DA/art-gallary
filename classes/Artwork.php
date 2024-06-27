<?php

class Artwork
{
    private $artistID;
    private $artworkID;
    private $title;
    private $yearOfWork;
    private $medium;
    private $width;
    private $height;
    private $description;
    private $genreNames;
    private $genreIDs;
    private $subjectNames;
    private $subjectIDs;
    private $artistName;
    private $galleryInfo;
    private $latitude;
    private $longitude;
    private $imageFilename;
    private $originalHome;

    /**
     * Constructor for the Artwork class.
     *
     * @param array $data An associative array containing artwork data.
     */
    public function __construct($data)
    {
        $this->artistID = $data['artistID'] ?? null;
        $this->artworkID = $data['artworkID'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->yearOfWork = $data['yearOfWork'] ?? null;
        $this->medium = $data['medium'] ?? '';
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
        $this->description = $data['description'] ?? '';
        $this->genreNames = $data['genreNames'] ?? '';
        $this->genreIDs = $data['genreIDs'] ?? '';
        $this->subjectNames = $data['subjectNames'] ?? '';
        $this->subjectIDs = $data['subjectIDs'] ?? '';
        $this->artistName = $data['artistName'] ?? '';
        $this->galleryInfo = $data['galleryInfo'] ?? '';
        $this->latitude = $data['latitude'] ?? null;
        $this->longitude = $data['longitude'] ?? null;
        $this->imageFilename = $data['imagefilename'] ?? '';
        $this->originalHome = $data['originalHome'] ?? '';
    }

    /**
     * Get the artist ID.
     *
     * @return int|null The ID of the artist who created the artwork.
     */
    public function getArtistID()
    {
        return $this->artistID;
    }

    /**
     * Get the artwork ID.
     *
     * @return int|null The ID of the artwork.
     */
    public function getArtworkID()
    {
        return $this->artworkID;
    }

    /**
     * Get the title of the artwork.
     *
     * @return string The title of the artwork.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the year the artwork was created.
     *
     * @return int|null The year the artwork was created.
     */
    public function getYearOfWork()
    {
        return $this->yearOfWork;
    }

    /**
     * Get the medium of the artwork.
     *
     * @return string The medium of the artwork.
     */
    public function getMedium()
    {
        return $this->medium;
    }

    /**
     * Get the width of the artwork.
     *
     * @return int|null The width of the artwork.
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the height of the artwork.
     *
     * @return int|null The height of the artwork.
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get the description of the artwork.
     *
     * @return string The description of the artwork.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the genre names associated with the artwork.
     *
     * @return string The genre names associated with the artwork.
     */
    public function getGenreNames()
    {
        return $this->genreNames;
    }

    /**
     * Get the genre IDs associated with the artwork.
     *
     * @return string The genre IDs associated with the artwork.
     */
    public function getGenreIDs()
    {
        return $this->genreIDs;
    }

    /**
     * Get the subject names associated with the artwork.
     *
     * @return string The subject names associated with the artwork.
     */
    public function getSubjectNames()
    {
        return $this->subjectNames;
    }

    /**
     * Get the subject IDs associated with the artwork.
     *
     * @return string The subject IDs associated with the artwork.
     */
    public function getSubjectIDs()
    {
        return $this->subjectIDs;
    }

    /**
     * Get the name of the artist.
     *
     * @return string The name of the artist who created the artwork.
     */
    public function getArtistName()
    {
        return $this->artistName;
    }

    /**
     * Get the gallery information.
     *
     * @return string The information about the gallery where the artwork is displayed.
     */
    public function getGalleryInfo()
    {
        return $this->galleryInfo;
    }

    /**
     * Get the latitude of the artwork's location.
     *
     * @return float|null The latitude of the artwork's location.
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get the longitude of the artwork's location.
     *
     * @return float|null The longitude of the artwork's location.
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Get the image filename of the artwork.
     *
     * @return string The filename of the image of the artwork.
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }
}

?>