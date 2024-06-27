<?php

class Customerlogin
{
    private $customerID;
    private $userName;
    private $pass;
    private $state;
    private $type;
    private $dateJoined;
    private $dateLastModified;

    /**
     * Constructor for the Customerlogin class.
     *
     * @param array $data An associative array containing customer login data.
     */
    public function __construct($data)
    {
        $this->customerID = $data['CustomerID'];
        $this->userName = $data['UserName'] ?? null;
        $this->pass = $data['Pass'] ?? null;
        $this->state = $data['State'] ?? null;
        $this->type = $data['Type'] ?? null;
        $this->dateJoined = $data['DateJoined'] ?? null;
        $this->dateLastModified = $data['DateLastModified'] ?? null;
    }

    /**
     * Get the customer ID.
     *
     * @return int The ID of the customer.
     */
    public function getCustomerID()
    {
        return $this->customerID;
    }

    /**
     * Set the customer ID.
     *
     * @param int $customerID The ID to set.
     */
    public function setCustomerID($customerID)
    {
        $this->customerID = $customerID;
    }

    /**
     * Get the username.
     *
     * @return string|null The username of the customer.
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set the username.
     *
     * @param string|null $userName The username to set.
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the password.
     *
     * @return string|null The password of the customer.
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set the password.
     *
     * @param string|null $pass The password to set.
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * Get the state of the customer.
     *
     * @return string|null The state of the customer.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the state of the customer.
     *
     * @param string|null $state The state to set.
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get the type of the customer.
     *
     * @return string|null The type of the customer.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the customer.
     *
     * @param string|null $type The type to set.
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the date the customer joined.
     *
     * @return string|null The date the customer joined.
     */
    public function getDateJoined()
    {
        return $this->dateJoined;
    }

    /**
     * Get the date the customer's details were last modified.
     *
     * @return string The date of last modification.
     */
    public function getDateLastModified()
    {
        return $this->dateLastModified;
    }
}

?>