<?php

class searchResult
{
    private $artworkID;
    private $artistID;
    private $title;
    private $artistName;
    private $yearOfWork;
    private $yearOfBirth;
    private $yearOfDeath;
    private $nationality;
    private $imageFilename;

    /**
     * Constructor for the searchResult class.
     *
     * @param array $data An associative array containing search result data.
     */
    public function __construct($data)
    {
        $this->setArtworkID($data['artworkID'] ?? null);
        $this->setArtistID($data['artistID'] ?? null);
        $this->setTitle($data['title'] ?? null);
        $this->setArtistName($data['artistName'] ?? null);
        $this->setYearOfWork($data['yearOfWork'] ?? null);
        $this->setYearOfBirth($data['yearOfBirth'] ?? null);
        $this->setYearOfDeath($data['yearOfDeath'] ?? null);
        $this->setNationality($data['nationality'] ?? null);
        $this->setImageFilename($data['imagefilename'] ?? null);
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
     * Set the artwork ID.
     *
     * @param int|null $artworkID The ID to set.
     */
    public function setArtworkID($artworkID)
    {
        $this->artworkID = $artworkID;
    }

    /**
     * Get the artist ID.
     *
     * @return int|null The ID of the artist.
     */
    public function getArtistID()
    {
        return $this->artistID;
    }

    /**
     * Set the artist ID.
     *
     * @param int|null $artistID The ID to set.
     */
    public function setArtistID($artistID)
    {
        $this->artistID = $artistID;
    }

    /**
     * Get the title.
     *
     * @return string|null The title of the artwork.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param string|null $title The title to set.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the artist's name.
     *
     * @return string|null The name of the artist.
     */
    public function getArtistName()
    {
        return $this->artistName;
    }

    /**
     * Set the artist's name.
     *
     * @param string|null $artistName The name to set.
     */
    public function setArtistName($artistName)
    {
        $this->artistName = $artistName;
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
     * Set the year the artwork was created.
     *
     * @param int|null $yearOfWork The year to set.
     */
    public function setYearOfWork($yearOfWork)
    {
        $this->yearOfWork = $yearOfWork;
    }

    /**
     * Get the year the artist was born.
     *
     * @return int|null The year the artist was born.
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Set the year the artist was born.
     *
     * @param int|null $yearOfBirth The year to set.
     */
    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;
    }

    /**
     * Get the year the artist died.
     *
     * @return int|null The year the artist died.
     */
    public function getYearOfDeath()
    {
        return $this->yearOfDeath;
    }

    /**
     * Set the year the artist died.
     *
     * @param int|null $yearOfDeath The year to set.
     */
    public function setYearOfDeath($yearOfDeath)
    {
        $this->yearOfDeath = $yearOfDeath;
    }

    /**
     * Get the nationality of the artist.
     *
     * @return string|null The nationality of the artist.
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set the nationality of the artist.
     *
     * @param string|null $nationality The nationality to set.
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * Get the filename of the artwork image.
     *
     * @return string|null The filename of the artwork image.
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * Set the filename of the artwork image.
     *
     * @param string|null $imageFilename The filename to set.
     */
    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;
    }
}
?>