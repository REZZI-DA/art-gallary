<?php

require_once('../config/database.php');
require_once('../repositories/searchRepository.php');

$db = new Database();
$db->connect();

$searchRepository = new SearchRepository($db);

/**
 * Function to sanitize input data for security.
 * Removes slashes and encodes special characters.
 *
 * @param string $input The input string to sanitize.
 * @return string The sanitized input string.
 */
function secureInput($input) {
    $input = stripslashes($input); // Remove backslashes
    $input = htmlspecialchars($input); // Encode special characters
    return $input;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $searchType = secureInput($_GET['searchType'] ?? 'artist');

    // Depending on searchType, sanitize respective input fields
    if ($searchType === 'artist') {
        $artistName = secureInput($_GET['artistName'] ?? '');
        $nationality = secureInput($_GET['nationality'] ?? '');
        $yearOfBirth = secureInput($_GET['yearOfBirth'] ?? '');
        $yearOfDeath = secureInput($_GET['yearOfDeath'] ?? '');

    } elseif ($searchType === 'artwork') {
        $artworkTitle = secureInput($_GET['artworkTitle'] ?? '');
        $genre = secureInput($_GET['genre'] ?? '');
        $yearOfWorkStart = secureInput($_GET['yearOfWorkStart'] ?? '');
        $yearOfWorkEnd = secureInput($_GET['yearOfWorkEnd'] ?? '');
    }
}

$db->close();
?>


<?php include '../includes/header.php'; ?>
<div class="advance">
    <div id="content" class="p-4 p-md-5">
        <div class="container light-style flex-grow-1 container-p-y" style="padding: 1rem; margin:1rem">
            <link rel="stylesheet" href="../assets/css/tables.css">

            <div class="container">
                <h1>Advanced Search</h1>
                <form action="searchresults.php" method="GET" id="searchForm">
                    <div class="mb-3">
                        <label for="searchType" class="form-label">Search Type</label>
                        <select name="searchType" id="searchType" class="form-select" required>
                            <option value="artist" <?php if ($searchType === 'artist') echo 'selected'; ?>>Artist</option>
                            <option value="artwork" <?php if ($searchType === 'artwork') echo 'selected'; ?>>Artwork</option>
                        </select>
                    </div>

                    <div id="artistFields" <?php if ($searchType !== 'artist') echo 'style="display:none;"'; ?>>
                        <div class="mb-3">
                            <label for="artistName" class="form-label">Artist Name</label>
                            <input type="text" name="artistName" id="artistName" class="form-control" value="<?php echo $artistName ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="yearOfBirth" class="form-label">Year of Birth</label>
                            <input type="number" name="yearOfBirth" id="yearOfBirth" class="form-control" value="<?php echo $yearOfBirth ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="yearOfDeath" class="form-label">Year of Death</label>
                            <input type="number" name="yearOfDeath" id="yearOfDeath" class="form-control" value="<?php echo $yearOfDeath ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" name="nationality" id="nationality" class="form-control" value="<?php echo $nationality ?? ''; ?>">
                        </div>
                    </div>

                    <div id="artworkFields" <?php if ($searchType !== 'artwork') echo 'style="display:none;"'; ?>>
                        <div class="mb-3">
                            <label for="artworkTitle" class="form-label">Artwork Title</label>
                            <input type="text" name="artworkTitle" id="artworkTitle" class="form-control" value="<?php echo $artworkTitle ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="yearOfWorkStart" class="form-label">Year of Work (Start)</label>
                            <input type="number" name="yearOfWorkStart" id="yearOfWorkStart" class="form-control" value="<?php echo $yearOfWorkStart ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="yearOfWorkEnd" class="form-label">Year of Work (End)</label>
                            <input type="number" name="yearOfWorkEnd" id="yearOfWorkEnd" class="form-control" value="<?php echo $yearOfWorkEnd ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <input type="text" name="genre" id="genre" class="form-control" value="<?php echo $genre ?? ''; ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to reset form fields when browser's back button is used
    window.addEventListener('pageshow', function(event) {
        var form = document.getElementById('searchForm');
        if (event.persisted || (typeof window.performance != 'undefined' && window.performance.navigation.type === 2)) {
            form.reset();
        }
    });

    // Toggle visibility of artist and artwork fields based on search type
    document.getElementById('searchType').addEventListener('change', function () {
        var searchType = this.value;
        if (searchType === 'artist') {
            document.getElementById('artistFields').style.display = 'block';
            document.getElementById('artworkFields').style.display = 'none';
        } else {
            document.getElementById('artistFields').style.display = 'none';
            document.getElementById('artworkFields').style.display = 'block';
        }
    });

</script>

<?php include '../includes/footer.php'; ?>

