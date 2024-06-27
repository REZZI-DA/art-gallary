<?php

if (!isset($_SESSION['favorite_artworks'])) {
    $_SESSION['favorite_artworks'] = [];
}

if (!isset($_SESSION['favorite_artists'])) {
    $_SESSION['favorite_artists'] = [];
}


if (isset($_POST['add_favorite_artwork'])) {
    addFavoriteArtwork($_POST['artworkID']);
}

if (isset($_POST['remove_favorite_artwork'])) {
    removeFavoriteArtwork($_POST['artworkID']);
}

if (isset($_POST['add_favorite_artist'])) {
    addFavoriteArtist($_POST['artistID']);
}

if (isset($_POST['remove_favorite_artist'])) {
    removeFavoriteArtist($_POST['artistID']);
}

/**
 * Gets the correct image path based on the provided directory and filename.
 * Checks for the existence of the file with or without a leading zero.
 * @param string $imageDirectory The directory containing the images.
 * @param string $imageFilename The base filename of the image.
 * @return string The correct image path or a default image path if not found.
 */
if (!function_exists('getCorrectImagePath')) {
    function getCorrectImagePath($imageDirectory, $imageFilename)
    {
        $filePathWithoutZero = $imageDirectory . $imageFilename . '.jpg';
        $filePathWithZero = $imageDirectory . '0' . $imageFilename . '.jpg';

        if (file_exists($filePathWithoutZero)) {
            return $filePathWithoutZero;
        }

        if (file_exists($filePathWithZero)) {
            return $filePathWithZero;
        }

        return $imageDirectory . 'default.jpg';
    }
}

/**
 * Generates HTML links for given names and IDs with a base URL.
 * @param string $names A semicolon-separated list of names.
 * @param string $ids A semicolon-separated list of IDs.
 * @param string $baseUrl The base URL to use for the links.
 * @return string The generated HTML links.
 */
if (!function_exists('generateLinks')) {
    function generateLinks($names, $ids, $baseUrl)
    {
        $namesArray = explode(';', $names);
        $idsArray = explode(';', $ids);
        $links = [];

        foreach ($namesArray as $index => $name) {
            $links[] = '<a href="' . htmlspecialchars($baseUrl) . '?id=' . htmlspecialchars($idsArray[$index]) . '">' . htmlspecialchars($name) . '</a>';
        }

        return implode('<br>', $links);
    }
}

/**
 * Adds an artwork to the session's list of favorite artworks.
 * @param string $artworkID The ID of the artwork to add.
 */
if (!function_exists('addFavoriteArtwork')) {
    function addFavoriteArtwork($artworkID)
    {
        if (!in_array($artworkID, $_SESSION['favorite_artworks'])) {
            $_SESSION['favorite_artworks'][] = $artworkID;
        }
    }
}

/**
 * Removes an artwork from the session's list of favorite artworks.
 * @param string $artworkID The ID of the artwork to remove.
 */
if (!function_exists('removeFavoriteArtwork')) {
    function removeFavoriteArtwork($artworkID)
    {
        if (($key = array_search($artworkID, $_SESSION['favorite_artworks'])) !== false) {
            unset($_SESSION['favorite_artworks'][$key]);
        }
    }
}

/**
 * Adds an artist to the session's list of favorite artists.
 * @param string $artistID The ID of the artist to add.
 */
if (!function_exists('addFavoriteArtist')) {
    function addFavoriteArtist($artistID)
    {
        if (!in_array($artistID, $_SESSION['favorite_artists'])) {
            $_SESSION['favorite_artists'][] = $artistID;
        }
    }
}

/**
 * Removes an artist from the session's list of favorite artists.
 * @param string $artistID The ID of the artist to remove.
 */
if (!function_exists('removeFavoriteArtist')) {
    function removeFavoriteArtist($artistID)
    {
        if (($key = array_search($artistID, $_SESSION['favorite_artists'])) !== false) {
            unset($_SESSION['favorite_artists'][$key]);
        }
    }
}

/**
 * Gets the details of favorite artworks or artists based on their type and ID.
 * @param object $repository The repository to fetch details from.
 * @param array $ids The list of IDs to fetch details for.
 * @param string $type The type of favorite ('artwork' or 'artist').
 * @return array The details of the favorites.
 */
if (!function_exists('getFavoriteDetails')) {
    function getFavoriteDetails($repository, $ids, $type)
    {
        $details = [];
        foreach ($ids as $id) {
            if ($type === 'artwork') {
                $details[] = $repository->getFavoriteArtworkByID($id);
            } elseif ($type === 'artist') {
                $details[] = $repository->getFavoriteArtistByID($id);
            }
        }
        return $details;
    }
}

/**
 * Checks if an item (artwork or artist) is in the session's list of favorites.
 * @param string $type The type of item ('artist' or 'artwork').
 * @param string $id The ID of the item to check.
 * @return bool True if the item is a favorite, false otherwise.
 */
if (!function_exists('isFavorite')) {
    function isFavorite($type, $id)
    {
        if ($type === 'artist') {
            return in_array($id, $_SESSION['favorite_artists'] ?? []);
        } elseif ($type === 'artwork') {
            return in_array($id, $_SESSION['favorite_artworks'] ?? []);
        }
    }
}

/**
 * Logs the user out by destroying the session and redirecting to the login page.
 */
if (!function_exists('logout')) {
    function logout()
    {

        session_start();

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }


        session_destroy();


        header("Location: ./pages/home.php");
        exit;
    }
}

/**
 * Generates a URL for a given filename in the 'pages' directory.
 * @param string $filename The name of the file to generate the URL for.
 * @return string The generated URL.
 */
if (!function_exists('generateUrl')) {
    function generateUrl($filename)
    {
        $parentFolderName = basename(dirname(dirname(__FILE__)));

        $folderName = "pages";
        return "/$parentFolderName/$folderName/$filename";
    }
}

/**
 * Validate if the given password meets the required criteria.
 *
 * Password must:
 * - Be between 8 to 20 characters long
 * - Contain at least one uppercase letter, one lowercase letter,
 *   one digit, and one special character from !@#$%&*_?
 *
 * @param string $password The password to validate.
 * @return bool Returns true if the password is valid, false otherwise.
 */
if (!function_exists('validatePassword')) {
    function validatePassword($password)
    {

        $pattern = '/^(?=.*[!@#$%&*_?])(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z\d!@#$%&*_?]{8,20}$/';
        return preg_match($pattern, $password);
    }
}

/**
 * Alias for validatePassword function.
 * Validate if the given password meets the required criteria.
 *
 * Password must:
 * - Be between 8 to 20 characters long
 * - Contain at least one uppercase letter, one lowercase letter,
 *   one digit, and one special character from !@#$%&*_?
 *
 * @param string $password The password to validate.
 * @return bool Returns true if the password is valid, false otherwise.
 */
if (!function_exists('is_validPass')) {
    function is_validPass($password)
    {

        $pattern = '/^(?=.*[!@#$%&*_?])(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z\d!@#$%&*_?]{8,20}$/';
        return preg_match($pattern, $password);
    }
}

/**
 * Validate if the given email address is valid.
 *
 * Email must:
 * - Start with alphanumeric characters, underscore, percent, plus, or minus
 * - Followed by @ symbol
 * - Followed by alphanumeric characters, dot, or hyphen
 * - End with a dot followed by 2 or more alphanumeric characters
 *
 * @param string $email The email address to validate.
 * @return bool Returns true if the email is valid, false otherwise.
 */
if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }
    
}

/**
 * Returns the country name for a given country code.
 *
 * This function fetches the JSON data of countries, decodes it,
 * and searches for the country name corresponding to the given country code.
 *
 * @param string $codecountry The country code to search for.
 * @return string The name of the country corresponding to the given country code, or "Land nicht gefunden" if the code is not found.
 */
if (!function_exists('getCountryNameByCode')) {
function getCountryNameByCode($codecountry) {
    $countriesJson = file_get_contents('https://cdn.jsdelivr.net/npm/countries-list/dist/countries.min.json');
    $countries = json_decode($countriesJson, true);
    
    foreach ($countries as $code => $country1) {
        if ($code === $codecountry) {
            return htmlspecialchars($country1['name']);
        }
    }
    
    return "Land nicht gefunden";
}
}

