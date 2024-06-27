<?php
require_once('../repositories/artworkRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$artworkRepo = new ArtworkRepository($db);

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];

// Check if user is admin or regular user
$isAdmin = isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] === true;
$isUser = isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true;

$artworkID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($artworkID === 0) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

try {
    // Fetch artwork details by ID
    $artwork = $artworkRepo->getArtworkByID($artworkID);
} catch (Exception $e) {
    echo "Error fetching artwork details: " . htmlspecialchars($e->getMessage());
    exit;
}

if (!$artwork) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

// Retrieve average rating and total reviews for the artwork
$averageRating = $artworkRepo->getArtworkReview($artworkID);
$totalReviews = $artworkRepo->getArtworkReview($artworkID);
$averageRating = $averageRating['AverageRating'];
$totalReviews = $totalReviews['TotalReviews'];

// Generate links for genres and subjects related to the artwork
$genreLinks = generateLinks($artwork->getGenreNames(), $artwork->getGenreIDs(), 'genre.php');
$subjectLinks = generateLinks($artwork->getSubjectNames(), $artwork->getSubjectIDs(), 'subject.php');
?>



<?php include __DIR__ . '/../includes/header.php'; ?>
<style>

    <title><?= ($artwork->getTitle()) ?></title>
    <style>
        #content {
            overflow-x: hidden;
        }

        .accordion {
            width: 95%;
        }

        .table th {
            width: 20%;
        }

        .table td {
            width: 80%;
        }

        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

    .custom-img-size {
        width: 100%; /* Das Bild wird 100% der Breite des Modals einnehmen */
        height: auto; /* Das Bild behält sein Seitenverhältnis bei */
    }
    </style>
<div class="main">
    <div id="content" class="p-4 p-md-5">
        <div class="container light-style flex-grow-1 container-p-y">
            <link rel="stylesheet" href="../assets/css/tables.css">
            <div class="container">

                <h1><?= ($artwork->getTitle()) ?></h1>
                <p>By
                    <a href="artist.php?id=<?= ($artwork->getArtistID()) ?>"><?= ($artwork->getArtistName()) ?></a>
                </p>

                <div class="row">
                    <div class="col-md-4">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <img src="<?= getCorrectImagePath('../assets/images/works/medium/', ($artwork->getImageFilename())) ?>"
                                 title="<?= ($artwork->getTitle()) ?>"
                                 alt="<?= ($artwork->getTitle()) ?>" class="img-fluid"/>
                        </a>
                        <p><br></p>
                        <p><a href="artworks.php" class="btn btn-primary">Artworks Overview</a></p>
                    </div>
                    <div class="col-md-1">
                    </div>

                    <div class="col-md-6">

                        <p><?= nl2br($artwork->getDescription()) ?></p>

                        <ul class="list-inline small">
                            <div class="list-inline-item m-0">
                                <?php

                                $roundedRating = ($totalReviews > 0) ? round($averageRating * 2) / 2 : 0.0;

                                // Anzeige der Sterne
                                for ($x = 1; $x <= 5; $x++): ?>
                                    <?php if ($x <= $roundedRating): ?>
                                        <i class="bi bi-star-fill stars"></i>
                                    <?php elseif ($x - 0.5 == $roundedRating): ?>
                                        <i class="bi bi-star-half stars"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star stars"></i>
                                    <?php endif; ?>
                                <?php endfor; echo $totalReviews ?>
                            </div>
                        </ul>

                        <button class="btn btn-sm btn-toggle-favorite btn-success"
                                data-type="artwork"
                                data-id="<?= ($artwork->getArtworkID()) ?>"
                                data-page="search"
                                data-favorite="<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'true' : 'false' ?>"
                                onclick="submitForm('artwork', <?= htmlspecialchars($artwork->getArtworkID()) ?>, '<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'remove' : 'add' ?>', false)">
                            <i class="<?= isFavorite('artwork', $artwork->getArtworkID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                        </button>

                        <p></p>
                        <div class="accordion" id="galleryAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="galleryHeading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#galleryCollapse" aria-expanded="true"
                                            aria-controls="galleryCollapse">
                                        Product Details
                                    </button>
                                </h2>
                                <div id="galleryCollapse" class="accordion-collapse collapse show"
                                     aria-labelledby="galleryHeading" data-bs-parent="#galleryAccordion">
                                    <div class="accordion-body">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <th>Date:</th>
                                                <td><?= ($artwork->getYearOfWork()) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Medium:</th>
                                                <td><?= ($artwork->getMedium()) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Dimensions:</th>
                                                <td><?= ($artwork->getWidth()) ?>cm
                                                    x <?= ($artwork->getHeight()) ?>cm
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Home:</th>
                                                <td><?= ($artwork->getGalleryInfo() ?: 'Unknown') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Genres:</th>
                                                <td><?= $genreLinks ?></td>
                                            </tr>
                                            <tr>
                                                <th>Subjects:</th>
                                                <td><?= $subjectLinks ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <div class="map-container">
                                            <iframe
                                                    src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $artwork->getLongitude() - 0.005 ?>%2C<?= $artwork->getLatitude() - 0.005 ?>%2C<?= $artwork->getLongitude() + 0.005 ?>%2C<?= $artwork->getLatitude() + 0.005 ?>&layer=mapnik&marker=<?= $artwork->getLatitude() ?>%2C<?= $artwork->getLongitude() ?>"
                                                    style="border: 1px solid black">
                                            </iframe>
                                        </div>
                                        <small>
                                            <a href="https://www.openstreetmap.org/?mlat=<?= htmlspecialchars($artwork->getLatitude()) ?>&mlon=<?= htmlspecialchars($artwork->getLongitude()) ?>#map=15/<?= htmlspecialchars($artwork->getLatitude()) ?>/<?= htmlspecialchars($artwork->getLongitude()) ?>">View
                                                Larger Map</a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>
        <form id="toggle-favorite-form" method="get" action="toggleFavorite.php" style="display: none;">
            <input type="hidden" name="type" id="type">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="favorites" id="favorites" value="">
            <input type="hidden" name="redirect" id="redirect"
                   value="<?php echo isset($_GET['favorites']) && $_GET['favorites'] === 'true' ? 'art-library/searchresults.php?favorites=true' : $fullUrl; ?>">
        </form>

    </div>

</div>
<?php include __DIR__ . '/../includes/review.php'; ?>
<p><br></p>

<!-- Modal for Image View -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl custom-modal-size2">
        <div class="modal-content">
            <div class="modal-body">
                <img src="<?= getCorrectImagePath('../assets/images/works/large/', ($artwork->getImageFilename())) ?>"
                     title="<?= ($artwork->getTitle()) ?>"
                     alt="<?= ($artwork->getTitle()) ?>" class="img-fluid custom-img-size"/>
            </div>
        </div>
    </div>
</div>



<script src="../scripts/favorites.js"></script>
<?php include '../includes/footer.php'; ?>
