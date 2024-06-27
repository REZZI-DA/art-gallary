<?php

require_once('../repositories/ArtistRepository.php');
require_once('../repositories/ArtworkRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$artistRepo = new ArtistRepository($db);
$artworkRepo = new ArtworkRepository($db);

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];

$artistID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($artistID === 0) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

try {
    // Fetch artist details by ID
    $artist = $artistRepo->getArtistByID($artistID);
} catch (Exception $e) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

if (!$artist) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

try {
    // Fetch artworks related to the artist
    $relatedArtworks = $artworkRepo->getArtworksByArtistID($artistID);
} catch (Exception $e) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}
?>



<?php include '../includes/header.php'; ?>
<style>
    .accordion {
        width: 95%;
    }

    .table th {
        width: 20%;
    }

    .table td {
        width: 80%;
    }
</style>
<title><?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></title>

<div class="main">
    <div id="content" class="p-4 p-md-5">


        <div class="container light-style flex-grow-1 container-p-y">
            <link rel="stylesheet" href="../assets/css/tables.css">
            <div class="container">


                <div class="row">
                    <div class="col-md-4">
                        <h1><?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></h1>
                        <p>
                            <img src="<?= getCorrectImagePath('../assets/images/artists/medium/', ($artist->getArtistID())); ?>"
                                 title="<?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>"
                                 alt="<?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?>"
                                 class="img-fluid"/>
                        </p>
                        <p><a href="artists.php" class="btn btn-primary">Artists Overview</a></p>
                    </div>
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-6">
                        <p><?= nl2br($artist->getDetails()) ?></p>
                        <button class="btn btn-sm btn-toggle-favorite btn-success"
                                data-type="artist"
                                data-id="<?= htmlspecialchars($artist->getArtistID()) ?>"
                                data-page="search"
                                data-favorite="<?= isFavorite('artist', $artist->getArtistID()) ? 'true' : 'false' ?>"
                                onclick="submitForm('artist', <?= htmlspecialchars($artist->getArtistID()) ?>, '<?= isFavorite('artist', $artist->getArtistID()) ? 'remove' : 'add' ?>', false)">
                            <i class="<?= isFavorite('artist', $artist->getArtistID()) ? 'bi bi-heart-fill' : 'bi bi-heart' ?>"></i>
                        </button>
                        <p></p>

                        <!-- Collapsible Accordion for Artist Information -->
                        <div class="accordion" id="artistAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="artistHeading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#artistCollapse" aria-expanded="true"
                                            aria-controls="artistCollapse">
                                        Artist Information
                                    </button>
                                </h2>
                                <div id="artistCollapse" class="accordion-collapse collapse show"
                                     aria-labelledby="artistHeading"
                                     data-bs-parent="#artistAccordion">
                                    <div class="accordion-body">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <th>Date:</th>
                                                <td><?= htmlspecialchars($artist->getYearOfBirth()) ?>
                                                    - <?= htmlspecialchars($artist->getYearOfDeath() ?: 'N/A') ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nationality:</th>
                                                <td><?= htmlspecialchars($artist->getNationality()) ?></td>
                                            </tr>
                                            <tr>
                                                <th>More Info:</th>
                                                <td><a href="<?= htmlspecialchars($artist->getArtistLink()) ?>"
                                                       target="_blank">Wikipedia</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                    </div>
                </div>
            </div>
            <p>
            <h3> Art By <?= htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()) ?></h3></p>
            <div class="row row-cols-1 row-cols-md-5 g-4">
                <?php foreach ($relatedArtworks as $artwork): ?>
                    <div class="col">
                        <div class="card h-100">
                            <a href="artwork.php?id=<?= $artwork->getArtworkID() ?>">
                                <img src="<?= getCorrectImagePath('../assets/images/works/square-medium/', $artwork->getImageFilename()) ?>"
                                     title="<?= htmlspecialchars($artwork->getTitle()) ?>"
                                     alt="<?= htmlspecialchars($artwork->getTitle()) ?>"
                                     class="card-img-top">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="artwork.php?id=<?= $artwork->getArtworkID() ?>"><?= htmlspecialchars($artwork->getTitle()) ?></a>
                                </h5>
                                <a href="artwork.php?id=<?= $artwork->getArtworkID() ?>"
                                   class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
</div>
<script src="../scripts/favorites.js"></script>
<?php include '../includes/footer.php'; ?>

