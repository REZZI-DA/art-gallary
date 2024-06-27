<?php
require_once('../includes/session.php');
require_once('../repositories/searchRepository.php');
require_once('../includes/functions.php');
require_once(__DIR__ . '/../classes/searchResult.php');

$db = new Database();
$db->connect();

// Sorting options and parameters for artists
$artistSortOptions = ['firstName', 'yearOfBirth'];
$artistSort = isset($_GET['artistSort']) && in_array($_GET['artistSort'], $artistSortOptions) ? $_GET['artistSort'] : 'firstName';
$artistOrder = isset($_GET['artistOrder']) ? $_GET['artistOrder'] : 'ASC';
$artistOrder = strtoupper($artistOrder) === 'DESC' ? 'DESC' : 'ASC';
$artistNextOrder = $artistOrder === 'ASC' ? 'DESC' : 'ASC';
$artistSecondarySort = $artistSort === 'firstName' ? 'lastName' : 'firstName';

// Sorting options and parameters for artworks
$artworkSortOptions = ['title', 'artistName', 'yearOfWork'];
$artworkSort = isset($_GET['artworkSort']) && in_array($_GET['artworkSort'], $artworkSortOptions) ? $_GET['artworkSort'] : 'title';
$artworkOrder = isset($_GET['artworkOrder']) ? $_GET['artworkOrder'] : 'ASC';
$artworkOrder = strtoupper($artworkOrder) === 'DESC' ? 'DESC' : 'ASC';
$artworkNextOrder = $artworkOrder === 'ASC' ? 'DESC' : 'ASC';
$artworkSecondarySort = $artworkSort === 'artistName' ? 'title' : ($artworkSort === 'title' ? 'yearOfWork' : 'title');

// Repository instance for handling search results
$searchResultRepository = new SearchRepository($db);

// Determine if favorites mode is active
$favorites = isset($_GET['favorites']) && $_GET['favorites'] === 'true' ? true : false;

// Parameters for search filtering
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : '';
$artistName = isset($_GET['artistName']) ? $_GET['artistName'] : '';
$artistYearofBirth = isset($_GET['yearOfBirth']) ? $_GET['yearOfBirth'] : '';
$artistYearofDeath = isset($_GET['yearOfDeath']) ? $_GET['yearOfDeath'] : '';
$artistNationality = isset($_GET['nationality']) ? $_GET['nationality'] : '';
$artworkTitle = isset($_GET['artworkTitle']) ? $_GET['artworkTitle'] : '';
$artworkYearStart = isset($_GET['yearOfWorkStart']) ? $_GET['yearOfWorkStart'] : '';
$artworkYearEnd = isset($_GET['yearOfWorkEnd']) ? $_GET['yearOfWorkEnd'] : '';
$artworkGenre = isset($_GET['genre']) ? $_GET['genre'] : '';
$simpleSearchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

// Initialize result variables
$results = [];
$resultsartworks = [];

// Check if in favorites mode or perform specific searches
if ($favorites) {
    $favoriteArtists = $_SESSION['favorite_artists'] ?? [];
    $favoriteArtworks = $_SESSION['favorite_artworks'] ?? [];

    // Load favorites from session
    $results['artists'] = getFavoriteDetails($searchResultRepository, $favoriteArtists, 'artist');
    $results['artworks'] = getFavoriteDetails($searchResultRepository, $favoriteArtworks, 'artwork');
} else {
    if ($searchType === 'artist') {
        $_SESSION['resultsartworks'] = []; // Clear artworks result array
        $results = $searchResultRepository->searchArtists($_GET, $artistSort, $artistOrder, $artistSecondarySort);
    } elseif ($searchType === 'artwork') {
        $_SESSION['results'] = []; // Clear artists result array
        $resultsartworks = $searchResultRepository->searchArtworks($_GET, $artworkSort, $artworkOrder, $artworkSecondarySort);
    } elseif (isset($_GET['simpleSearch']) && $_GET['simpleSearch'] === 'true') {
        $_SESSION['results'] = []; // Clear artists result array for simple search
        $resultsartworks = $searchResultRepository->simpleSearch($_GET['searchTerm'], $artworkSort, $artworkOrder, $artworkSecondarySort);
    }
}

// Save results in session
$_SESSION['results'] = $results;
$_SESSION['resultsartworks'] = $resultsartworks;

$db->close();
?>

<?php include '../includes/header.php'; ?>
<div style="
    padding: 4rem 9rem;
    margin-left: -10rem;
    min-height: calc(100vh - 100px);
    position: static;
">

    <div id="content" class="p-4 p-md-5">


        <div class="container light-style flex-grow-1 container-p-y" style=" padding: 1rem; margin:1rem">
            <link rel="stylesheet" href="../assets/css/SearchTable.css">

            <h1></h1>
            <h5>
                <?php
                // Ausgabe für Artists
                if ($searchType === 'artist') {
                    echo "Search Results for Artists: ";
                    if ($artistName) echo $artistName;
                    if ($artistYearofBirth) echo " Birth: ", $artistYearofBirth;
                    if ($artistYearofDeath) echo " Death: ", $artistYearofDeath;
                    if ($artistNationality) echo " Nationality: ", $artistNationality;
                }

                // Ausgabe für Artworks
                if ($searchType === 'artwork') {
                    echo "Search Results for Artworks: ";
                    if ($artworkTitle) echo $artworkTitle;
                    if ($artworkYearStart) echo " Start: ", $artworkYearStart;
                    if ($artworkYearEnd) echo " End: ", $artworkYearEnd;
                    if ($artworkGenre) echo " Genre: ", $artworkGenre;
                }

                // Ausgabe für Simple Search
                if ($simpleSearchTerm) {
                    echo "Search Results for: ", $simpleSearchTerm;
                }
                ?>
            </h5>

            <?php if ($favorites): ?>
                <h2>Favorite Artists</h2>
                <?php if (count($results['artists']) > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="yellow">Artist Name</th>
                            <th class="yellow">Year of Birth</th>
                            <th class="yellow">Year of Death</th>
                            <th class="yellow">Image</th>
                            <th class="yellow">Favorite</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results['artists'] as $result): ?>
                            <tr>
                                <td>
                                    <a class="white-link"
                                       href="artist.php?id=<?= $result->getArtistID() ?>"><?= htmlspecialchars($result->getArtistName()) ?></a>
                                </td>
                                <td><?= htmlspecialchars($result->getYearOfBirth()) ?></td>
                                <td><?= htmlspecialchars($result->getYearOfDeath()) ?></td>
                                <td>
                                    <img src="<?= getCorrectImagePath('../assets/images/artists/square-thumb/', ($result->getArtistID())) ?>"
                                         title="<?= htmlspecialchars($result->getArtistName()) ?>"
                                         alt="<?= htmlspecialchars($result->getArtistName()) ?>"></td>
                                <td>
                                    <button class="btn btn-sm btn-toggle-favorite btn-success"
                                            data-type="artist"
                                            data-id="<?= htmlspecialchars($result->getArtistID()) ?>"
                                            data-page="search"
                                            data-favorite="<?= isFavorite('artist', $result->getArtistID()) ? 'true' : 'false' ?>"
                                            onclick="submitForm('artist', <?= htmlspecialchars($result->getArtistID()) ?>, '<?= isFavorite('artist', $result->getArtistID()) ? 'remove' : 'add' ?>', false)">
                                        <i class="<?= isFavorite('artist', $result->getArtistID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No favorite artists found.</p>
                <?php endif; ?>

                <h2>Favorite Artworks</h2>
                <?php if (count($results['artworks']) > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="yellow">Title</th>
                            <th class="yellow">Artist Name</th>
                            <th class="yellow">Year of Work</th>
                            <th class="yellow">Image</th>
                            <th class="yellow">Favorite</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results['artworks'] as $result): ?>
                            <tr>
                                <td>
                                    <a class="white-link"
                                       href="artwork.php?id=<?= $result->getArtworkID() ?>"><?= htmlspecialchars($result->getTitle()) ?></a>
                                </td>
                                <td>
                                    <a class="white-link"
                                       href="artist.php?id=<?= $result->getArtistID() ?>"><?= htmlspecialchars($result->getArtistName()) ?></a>
                                </td>
                                <td><?= htmlspecialchars($result->getYearOfWork()) ?></td>
                                <td>
                                    <img src="<?= getCorrectImagePath('../assets/images/works/square-small/', ($result->getImageFilename())) ?>"
                                         title="<?= htmlspecialchars($result->getTitle()) ?>"
                                         alt="<?= htmlspecialchars($result->getTitle()) ?>"></td>
                                <td>


                                    <button class="btn btn-sm btn-toggle-favorite btn-success"
                                            data-type="artwork"
                                            data-id="<?= htmlspecialchars($result->getArtworkID()) ?>"
                                            data-page="search"
                                            data-favorite="<?= isFavorite('artwork', $result->getArtworkID()) ? 'true' : 'false' ?>"
                                            onclick="submitForm('artwork', <?= htmlspecialchars($result->getArtworkID()) ?>, '<?= isFavorite('artwork', $result->getArtworkID()) ? 'remove' : 'add' ?>', false)">
                                        <i class="<?= isFavorite('artwork', $result->getArtworkID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                                    </button>


                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No favorite artworks found.</p>
                <?php endif; ?>

            <?php else: ?>
                <?php if (count($results) > 0 || count($resultsartworks) > 0): ?>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <?php if ($searchType === 'artist'): ?>

                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artistSort' => 'firstName', 'artistOrder' => $artistSort === 'firstName' ? $artistNextOrder : 'ASC'])) ?>">Artist <?= $artistSort === 'firstName' ? ($artistOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artistSort' => 'yearOfBirth', 'artistOrder' => $artistSort === 'yearOfBirth' ? $artistNextOrder : 'ASC'])) ?>">Year
                                        of
                                        Birth <?= $artistSort === 'yearOfBirth' ? ($artistOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">Year of Death</th>
                                <th class="yellow">Image</th>
                                <th class="yellow">Favorite</th>
                            <?php elseif ($searchType === 'artwork'): ?>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'title', 'artworkOrder' => $artworkSort === 'title' ? $artworkNextOrder : 'ASC'])) ?>">Title <?= $artworkSort === 'title' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'artistName', 'artworkOrder' => $artworkSort === 'artistName' ? $artworkNextOrder : 'ASC'])) ?>">Artist <?= $artworkSort === 'artistName' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'yearOfWork', 'artworkOrder' => $artworkSort === 'yearOfWork' ? $artworkNextOrder : 'ASC'])) ?>">Year <?= $artworkSort === 'yearOfWork' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">Image</th>
                                <th class="yellow">Favorite</th>
                            <?php elseif (isset($_GET['simpleSearch']) && $_GET['simpleSearch'] === 'true'): ?>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'title', 'artworkOrder' => $artworkSort === 'title' ? $artworkNextOrder : 'ASC'])) ?>">Title <?= $artworkSort === 'title' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th class="yellow">
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'artistName', 'artworkOrder' => $artworkSort === 'artistName' ? $artworkNextOrder : 'ASC'])) ?>">Artist <?= $artworkSort === 'artistName' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">
                                    <a class="white-link"
                                       href="?<?= http_build_query(array_merge($_GET, ['artworkSort' => 'yearOfWork', 'artworkOrder' => $artworkSort === 'yearOfWork' ? $artworkNextOrder : 'ASC'])) ?>">Year <?= $artworkSort === 'yearOfWork' ? ($artworkOrder === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow">Image</th>
                                <th class="yellow">Favorite</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $result): ?>
                            <?php if ($searchType === 'artist'): ?>
                                <tr>
                                    <td>
                                        <a href="artist.php?id=<?= $result->getArtistID() ?>"><?= htmlspecialchars($result->getArtistName()) ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($result->getYearOfBirth()) ?></td>
                                    <td><?= htmlspecialchars($result->getYearOfDeath()) ?></td>
                                    <td>
                                        <img src="<?= getCorrectImagePath('../assets/images/artists/square-thumb/', $result->getArtistID()) ?>"
                                             title="<?= htmlspecialchars($result->getArtistName()) ?>"
                                             alt="<?= htmlspecialchars($result->getArtistName()) ?>">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-toggle-favorite btn-success"
                                                data-type="artist"
                                                data-id="<?= htmlspecialchars($result->getArtistID()) ?>"
                                                data-page="search"
                                                data-favorite="<?= isFavorite('artist', $result->getArtistID()) ? 'true' : 'false' ?>"
                                                onclick="submitForm('artist', <?= htmlspecialchars($result->getArtistID()) ?>, '<?= isFavorite('artist', $result->getArtistID()) ? 'remove' : 'add' ?>', false)">
                                            <i class="<?= isFavorite('artist', $result->getArtistID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (!empty($resultsartworks)): ?>
                            <?php foreach ($resultsartworks as $artwork): ?>
                                <tr>
                                    <td>
                                        <a href="artwork.php?id=<?= $artwork->getArtworkID() ?>"><?= htmlspecialchars($artwork->getTitle()) ?></a>
                                    </td>
                                    <td>
                                        <a href="artist.php?id=<?= $artwork->getArtistID() ?>"><?= htmlspecialchars($artwork->getArtistName()) ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($artwork->getYearOfWork()) ?></td>
                                    <td>
                                        <img src="<?= getCorrectImagePath('../assets/images/works/square-small/', $artwork->getImageFilename()) ?>"
                                             title="<?= htmlspecialchars($artwork->getTitle()) ?>"
                                             alt="<?= htmlspecialchars($artwork->getTitle()) ?>">
                                    </td>
                                    <td>


                                        <button class="btn btn-sm btn-toggle-favorite btn-success"
                                                data-type="artwork"
                                                data-id="<?= htmlspecialchars($artwork->getArtworkID()) ?>"
                                                data-page="search"
                                                data-favorite="<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'true' : 'false' ?>"
                                                onclick="submitForm('artwork', <?= htmlspecialchars($artwork->getArtworkID()) ?>, '<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'remove' : 'add' ?>', false)">
                                            <i class="<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                                        </button>


                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No results found. Please adjust your search criteria.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <form id="toggle-favorite-form" method="get" action="toggleFavorite.php" style="display: none;">
            <input type="hidden" name="type" id="type">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="favorites" id="favorites" value="">
            <input type="hidden" name="redirect" id="redirect"
                   value="<?php echo isset($_GET['favorites']) && $_GET['favorites'] === 'true' ? 'art-library/searchresults.php?favorites=true' : $fullUrl; ?>">
        </form>

        <!-- Weitere Formularfelder hier -->


    </div>
</div>
</div>
<script src="../scripts/favorites.js"></script>

<?php include '../includes/footer.php'; ?>


