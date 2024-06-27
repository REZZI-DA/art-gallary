
<?php
require_once('../config/database.php');
require_once('../repositories/searchRepository.php');
require_once(__DIR__ . '/../config/database.php');
/*include __DIR__ . '/../classes/Customer.php';
include __DIR__ . '/../classes/Customerlogin.php';
*/
if (!class_exists('Customer')) {
    require_once(__DIR__ . '/../classes/Customer.php');
}

if (!class_exists('Customerlogin')) {
    require_once(__DIR__ . '/../classes/Customerlogin.php');
}


if (session_status() == PHP_SESSION_NONE) {
    session_start();

}
?>
