<?php

class Customer implements Serializable
{
    private $customerID;
    private $firstName;
    private $lastName;
    private $address;
    private $city;
    private $region;
    private $country;
    private $postal;
    private $phone;
    private $email;
    private $typ;
    private $status;

    /**
     * Constructor for the Customer class.
     *
     * @param array $data An associative array containing customer data.
     */
    public function __construct($data)
    {
        $this->customerID = $data['CustomerID'];
        $this->firstName = $data['FirstName'];
        $this->lastName = $data['LastName'];
        $this->address = $data['Address'];
        $this->city = $data['City'];
        $this->region = $data['Region'];
        $this->country = $data['Country'];
        $this->postal = $data['Postal'];
        $this->phone = $data['Phone'];
        $this->email = $data['Email'];
    }

    /**
     * Serialize the customer data.
     *
     * @return string Serialized customer data.
     */
    public function serialize()
    {
        return serialize(
            [
                'customerID' => $this->customerID,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'address' => $this->address,
                'city' => $this->city,
                'region' => $this->region,
                'country' => $this->country,
                'postal' => $this->postal,
                'phone' => $this->phone,
                'email' => $this->email,
                'typ' => $this->typ,
                'status' => $this->status,
            ]
        );
    }

    /**
     * Unserialize the customer data.
     *
     * @param string $data Serialized customer data.
     */
    public function unserialize($data)
    {
        $unserialized = unserialize($data);
        if (is_array($unserialized) === true) {
            foreach ($unserialized as $property => $value) {
                $this->{$property} = $value;
            }
        }
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
     * Get the first name of the customer.
     *
     * @return string The first name of the customer.
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the first name of the customer.
     *
     * @param string $firstName The first name to set.
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get the last name of the customer.
     *
     * @return string The last name of the customer.
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the last name of the customer.
     *
     * @param string $lastName The last name to set.
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get the address of the customer.
     *
     * @return string The address of the customer.
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the address of the customer.
     *
     * @param string $address The address to set.
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get the city of the customer.
     *
     * @return string The city of the customer.
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the city of the customer.
     *
     * @param string $city The city to set.
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get the region of the customer.
     *
     * @return string The region of the customer.
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set the region of the customer.
     *
     * @param string $region The region to set.
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * Get the country of the customer.
     *
     * @return string The country of the customer.
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the country of the customer.
     *
     * @param string $country The country to set.
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get the postal code of the customer.
     *
     * @return string The postal code of the customer.
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Set the postal code of the customer.
     *
     * @param string $postal The postal code to set.
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;
    }

    /**
     * Get the phone number of the customer.
     *
     * @return string The phone number of the customer.
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the phone number of the customer.
     *
     * @param string $phone The phone number to set.
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get the email of the customer.
     *
     * @return string The email of the customer.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email of the customer.
     *
     * @param string $email The email to set.
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get the type of the customer.
     *
     * @return string The type of the customer.
     */
    public function getTyp()
    {
        return $this->typ;
    }

    /**
     * Set the type of the customer.
     *
     * @param string $typ The type to set.
     */
    public function setTyp($typ)
    {
        $this->typ = $typ;
    }

    /**
     * Get the status of the customer.
     *
     * @return string The status of the customer.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status of the customer.
     *
     * @param string $status The status to set.
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}

?>