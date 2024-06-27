<?php
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $protocol = "https://";
} else {
    $protocol = "http://";
}

$host = $_SERVER['HTTP_HOST'];

// Root-URL für die Home-Seite zusammenstellen
$uri = $protocol . $host . '/art-gallary/pages/home.php';

// Weiterleitung zur Home-Seite
header('Location: ' . $uri);
exit;
?>
