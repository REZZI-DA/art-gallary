<?php

class Artist
{
    private $artistID;
    private $firstName;
    private $lastName;
    private $nationality;
    private $yearOfBirth;
    private $yearOfDeath;
    private $details;
    private $artistLink;
    private $reviewCount;

    /**
     * Constructor for the Artist class.
     *
     * @param array $data An associative array containing artist data.
     */
    public function __construct($data)
    {
        $this->artistID = $data['artistID'] ?? null;
        $this->firstName = $data['firstName'] ?? '';
        $this->lastName = $data['lastName'] ?? '';
        $this->nationality = $data['nationality'] ?? '';
        $this->yearOfBirth = $data['yearOfBirth'] ?? null;
        $this->yearOfDeath = $data['yearOfDeath'] ?? null;
        $this->details = isset($data['details']) ? $data['details'] : '';
        $this->artistLink = isset($data['artistLink']) ? $data['artistLink'] : '';
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
     * Get the first name of the artist.
     *
     * @return string The first name of the artist.
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get the last name of the artist.
     *
     * @return string The last name of the artist.
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get the nationality of the artist.
     *
     * @return string The nationality of the artist.
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Get the year of birth of the artist.
     *
     * @return int|null The year the artist was born.
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Get the year of death of the artist.
     *
     * @return int|null The year the artist died.
     */
    public function getYearOfDeath()
    {
        return $this->yearOfDeath;
    }

    /**
     * Get the details of the artist.
     *
     * @return string Additional details about the artist.
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Get the artist link.
     *
     * @return string A link related to the artist.
     */
    public function getArtistLink()
    {
        return $this->artistLink;
    }

    /**
     * Get the review count.
     *
     * @return int The number of reviews for the artist.
     */
    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    /**
     * Set the review count.
     *
     * @param int $reviewCount The number of reviews to set.
     */
    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;
    }
}

?>