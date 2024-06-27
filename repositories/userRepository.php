<?php
include __DIR__ . '/../classes/Customer.php';
include __DIR__ . '/../classes/Customerlogin.php';

/**
 * Class userRepository
 * This class manages user-related database operations such as login, registration, and user details.
 */
class userRepository
{
    /** @var Database $database Database connection object */
    private $database;

    /**
     * Constructor for userRepository class.
     * @param Database $db The database connection object.
     */
    function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * Verifies user login credentials.
     * @param string $username The username.
     * @param string $password The password.
     * @return bool True if login is successful, false otherwise.
     */
    public function verifyUserLogin($username, $password)
    {


        $this->database->connect();
        $sql = "SELECT * FROM customerlogon WHERE UserName = :username";
        $stmt = $this->database->prepareStatement($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();

        // Verify password hash and return true or false
        $this->database->close();
        return password_verify($password, $row["Pass"]);

    }

    /**
     * Retrieves user details by username.
     * @param string $username The username.
     * @return Customerlogin|null Customerlogin object if found, otherwise null.
     */
    public function getUser($username)
    {
        try {
            $this->database->connect();
            $stmt = $this->database->prepareStatement("SELECT *  FROM customerlogon WHERE UserName = :username");

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
         
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $customerlogon = new Customerlogin($result);
            $this->database->close();
     
            return $customerlogon;
        } catch (PDOException $e) {
            // Fehlerbehandlung im Falle einer Ausnahme
            $this->database->close();
            echo "Fehler beim datenbank abfrage " . $e->getMessage();
            return null;
        }
    }

    /**
     * Registers a new user.
     * @param string $password The password.
     * @param string $firstName User's first name.
     * @param string $lastName User's last name.
     * @param string $address User's address.
     * @param string $city User's city.
     * @param string $country User's country.
     * @param string $region User's region.
     * @param string $postal User's postal code.
     * @param string $phone User's phone number.
     * @param string $email User's email address.
     * @return bool True if registration is successful, false otherwise.
     */
    public function registerUser($password, $firstName, $lastName, $address, $city, $country, $region, $postal, $phone, $email)
    {
        try {

            // Hash des Passworts erstellen
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

            $this->database->connect();

            // Vordefinierte Werte für den Einfügebefehl

            $salt = $this->database->generateSalt();
            $state = 1; // active
            $type = 0; // normal user
            $dateJoined = date("Y-m-d H:i:s");
            $dateLastModified = date("Y-m-d H:i:s");

            // SQL-INSERT-Befehl vorbereiten
            $stmt = $this->database->prepareStatement("INSERT INTO customerlogon (UserName, Pass, Salt, State, Type, DateJoined, DateLastModified) 
                                VALUES (:username, :password, :salt, :state, :type, :dateJoined, :dateLastModified)");
            $stmt->bindParam(':username', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':salt', $salt);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':dateJoined', $dateJoined);
            $stmt->bindParam(':dateLastModified', $dateLastModified);
            $stmt->execute();
            $customerID = $this->database->getLastInsertedId();

            // INSERT-Abfrage vorbereiten und ausführen
            $sql = "INSERT INTO customers (CustomerID, firstName, lastName, address, city, region, country, postal, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute([$customerID, $firstName, $lastName, $address, $city, $region, $country, $postal, $phone, $email]);
            $this->database->close();
            return true;
        } catch (PDOException $e) {
            // Fehlerbehandlung im Falle einer Ausnahme
            $this->database->close();
            echo "Fehler beim Registrieren des Benutzers: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Deactivates a customer by setting their state to 0.
     * @param int $customerID The customer ID.
     */
    public function deactiveCustomer($customerID)
    {
        try {
            $this->database->connect();

            // SQL-UPDATE-Anweisung für die Tabelle customers
            $sqlCustomer = "UPDATE customerlogon SET state = 0 WHERE customerID = :customerId";
            $stmtCustomer = $this->database->prepareStatement($sqlCustomer);
            $stmtCustomer->bindParam(':customerId', $customerID, PDO::PARAM_INT);
            $stmtCustomer->execute();

            $this->database->close();
        } catch (PDOException $ex) {
            $this->database->close();
            exit('DB connection failed: ' . $ex->getMessage());
        }
    }

    /**
     * Updates customer details.
     * @param Customer $customer The Customer object with updated details.
     * @return bool True if update is successful, false otherwise.
     */
    public function updateCustomer($customer)
    {

        try {
            $this->database->connect();
            $sql = "UPDATE customers 
        SET 
            FirstName = :firstName, 
            LastName = :lastName, 
            Address = :address, 
            City = :city, 
            Region = :region, 
            Country = :country, 
            Postal = :postal, 
            Phone = :phone, 
            Email = :email 
        WHERE 
            CustomerID = :customerId";

            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute([':customerId' => $customer->getCustomerID(), ':firstName' => $customer->getFirstName(), ':lastName' => $customer->getLastName(), ':address' => $customer->getAddress(), ':city' => $customer->getCity(), ':region' => $customer->getRegion(), ':country' => $customer->getCountry(), ':postal' => $customer->getPostal(), ':phone' => $customer->getPhone(), ':email' => $customer->getEmail(),]);

            $this->database->close();
            return true;
        } catch (PDOException $ex) {
            $this->database->close();
            return false;
        }
    }
/**
 * Check if a username (email) already exists in the database.
 * @param string $username The username (email) to check.
 * @return bool True if the username (email) exists, false otherwise.
 */
public function isUsernameExists($username)
{
    try {
        $this->database->connect();
        
        $stmt = $this->database->prepareStatement("SELECT COUNT(*) AS count FROM customerlogon WHERE UserName = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->database->close();
        
        return intval($result['count']) > 0;
        
    } catch (PDOException $e) {
       
        $this->database->close();
        return false; 
    }
}
    
    /**
     * Retrieves all active users.
     * @return array Array of Customer objects representing active users.
     */
    public function getAllusers()
    {

        $this->database->connect();
        $sql = "
        SELECT c.*, cl.state, cl.type
        FROM customers c
        JOIN customerlogon cl ON c.customerID = cl.customerID
        WHERE cl.State = 1";

        $stmt = $this->database->prepareStatement($sql);
        $stmt->execute();

        $customers = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $customer = new Customer($result);

            // Setzen des Status (State)
            $customer->setStatus($result['state']);

            // Setzen des Typs
            $customer->setTyp($result['type']);

            $customers[] = $customer;
        }

        $this->database->close();

        return $customers;
    }

    /**
     * Updates customer login details.
     * @param Customerlogin $customerLogon The Customerlogin object with updated details.
     * @return bool True if update is successful, false otherwise.
     */
    public function updateCustomerlogon($customerLogon)
    {

        try {
            $this->database->connect();

            $sql = "UPDATE customerlogon 
                    SET 
                        UserName = COALESCE(:userName, UserName), 
                        Pass = COALESCE(:pass, Pass), 
                        State = COALESCE(:state, State), 
                        Type = COALESCE(:type, Type), 
                        DateJoined = COALESCE(:dateJoined, DateJoined), 
                        DateLastModified = :dateLastModified 
                    WHERE 
                        CustomerID = :customerId";

            $stmt = $this->database->prepareStatement($sql);
            $stmt->execute([':customerId' => $customerLogon->getCustomerID(), ':userName' => $customerLogon->getUserName(), ':pass' => $customerLogon->getPass(), ':state' => $customerLogon->getState(), ':type' => $customerLogon->getType(), ':dateJoined' => $customerLogon->getDateJoined(), ':dateLastModified' => date('Y-m-d H:i:s'), // Aktuelles Datum der letzten Änderung
            ]);

            $this->database->close();
            return true;

        } catch (PDOException $ex) {
            $this->database->close();
            return false;
        }
    }

    /**
     * Retrieves user details by customer ID.
     * @param int $customerId The customer ID.
     * @return Customer|null Customer object if found, otherwise null.
     */
    public function getUserbyId($customerId)
    {
        try {
            $this->database->connect();
            $sql = "SELECT * FROM customers WHERE CustomerID = :customerId";
        
            $stmt = $this->database->prepareStatement($sql);
            $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            $this->database->close();
      

            if ($result) {
                return new Customer($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->database->close();
            error_log('Database error: ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            error_log('General error: ' . $e->getMessage());
            return null;
        } finally {
            $this->database->close();

        }

    }
}

?>
