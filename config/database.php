<?php

require_once("constants.php");

class Database
{
    private $dsn ;
    private $user ;
    private $password;
    private $pdo;

    /**
     * Connects to the database using the provided DSN, username, and password.
     * Throws an exception if already connected or if the connection fails.
     */
    public function connect()
    {
        if ($this->isConnected()) {
            throw new Exception("DB already connected");
        }
        try {
            $this->loadEnv(__DIR__ . '/.env');
            $this->dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
            $this->user = $_ENV['DB_USER'];
            $this->password = $_ENV['DB_PASS'];
            $this->pdo = new PDO($this->dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            exit('DB connection failed: ' . $ex->getMessage());
        }
    }

    /**
     * Checks if the database is currently connected.
     * @return bool True if connected, false otherwise.
     */
    public function isConnected()
    {
        return $this->pdo !== null;
    }

    /**
     * Closes the database connection.
     */
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * Prepares an SQL statement for execution.
     * @param string $sql The SQL query to prepare.
     * @return PDOStatement|null The prepared statement or null if not connected.
     */
    public function prepareStatement($sql)
    {
        if (!$this->isConnected()) {
            return;
        }
        return $this->pdo->prepare($sql);
    }

    /**
     * Gets the ID of the last inserted row.
     * Throws an exception if not connected or if there is an error.
     * @return string The last inserted ID.
     * @throws Exception If not connected or if there is an error.
     */
    public function getLastInsertedId()
    {
        if (!$this->isConnected()) {
            throw new Exception("Not connected to the database");
        }

        try {
            $stmt = $this->pdo->query("SELECT LAST_INSERT_ID() AS last_id");
            $lastInsertId = $stmt->fetchColumn();
            return $lastInsertId;
        } catch (PDOException $ex) {
            throw new Exception("Error getting last inserted ID: " . $ex->getMessage());
        }
    }

    /**
     * Generates a random salt for cryptographic purposes.
     * @param int $length The length of the salt in bytes.
     * @return string The generated salt in hexadecimal format.
     */
    public function generateSalt($length = 16)
    {
        return bin2hex(random_bytes($length));
    }

/**
 * @param mixed $name
 * @return mixed
 */
    private function loadEnv($path) {
        if (!file_exists($path)) {
            throw new Exception('.env file not found');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
    }
}

?>


<!--


$dsn = 'mysql:host=' .DB_HOST .';dbname='. DB_NAME;
$user = DB_USER;
$password = DB_PASS;

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    exit('DB connection failed: '. $ex->getMessage());
}
// new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
// if($conn->connect_error){
//     die('Connection failed' .$conn->connect_error);
// }
// echo 'Connected!'; -->
