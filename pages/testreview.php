<?php
require_once('../repositories/artworkRepository.php');
require_once('../includes/functions.php');

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


$db = new Database();
?>
<?php include '../includes/header.php'; ?>


<?php include __DIR__ . '/../includes/review.php'; ?>
<?php include '../includes/footer.php'; ?>

