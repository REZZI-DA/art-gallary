<?php
require_once('../includes/session.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check if all required parameters are set and action is either 'add' or 'remove'
if ($type && $id && ($action === 'add' || $action === 'remove')) {
    // Define session key based on type
    $sessionKey = $type === 'artist' ? 'favorite_artists' : 'favorite_artworks';

    // Initialize session variable if not set
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = [];
    }

    // Add ID to favorites if action is 'add' and it's not already present
    if ($action === 'add' && !in_array($id, $_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey][] = $id;
    }
    // Remove ID from favorites if action is 'remove'
    elseif ($action === 'remove') {
        $_SESSION[$sessionKey] = array_diff($_SESSION[$sessionKey], [$id]);
    }
}

// Redirect to the current page to reload and update favorites
header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
?>
