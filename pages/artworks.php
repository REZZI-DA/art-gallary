<?php
require_once('../config/database.php');
require_once('../repositories/artworkRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$artworkRepo = new ArtworkRepository($db);

// Define sorting parameters
$sortOptions = ['title', 'artistName', 'yearOfWork'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';

// Define secondary sorting
$secondarySort = $sort === 'artistName' ? 'title' : ($sort === 'title' ? 'yearOfWork' : 'title');

try {
    // Retrieve all artworks with specified sorting
    $artworks = $artworkRepo->getAllArtworks($sort, $order, $secondarySort);
} catch (Exception $e) {
    echo "Error fetching artworks: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<?php
$parentFolderName = basename(dirname(dirname(__FILE__)));
?>

<?php include '../includes/header.php'; ?>

<div>

    <div id="content" class="p-4 p-md-5">

        <div class="container light-style flex-grow-1 container-p-y" style=" padding: 1rem; margin:1rem">
            <link rel="stylesheet" href="../assets/css/tables.css">
            <div class="container">
                <h1 class="table-title">Browse Artworks</h1>


                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="yellow" style="width: 45%">
                                <a class="white-link" href="?sort=title&order=<?= $sort === 'title' ? $nextOrder : 'ASC' ?>">Title <?= $sort === 'title' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                            </th>
                            <th class="yellow" style="width: 30%"><a class="white-link" href="?sort=artistName&order=<?= $sort === 'artistName' ? $nextOrder : 'ASC' ?>">Artist <?= $sort === 'artistName' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                            </th>
                            <th class="yellow" style="width: 20%">
                                <a class="white-link" href="?sort=yearOfWork&order=<?= $sort === 'yearOfWork' ? $nextOrder : 'ASC' ?>">Year <?= $sort === 'yearOfWork' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                            </th>
                            <th class="yellow" style="width: 5%">Image</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($artworks as $artwork): ?>
                            <tr onclick="window.location='artwork.php?id=<?= $artwork->getArtworkID() ?>';">
                                <td>
                                    <a href="artwork.php?id=<?= $artwork->getArtworkID() ?>"><?= htmlspecialchars($artwork->getTitle()) ?></a>
                                </td>
                                <td>
                                    <a href="artist.php?id=<?= $artwork->getArtistID() ?>"><?= htmlspecialchars($artwork->getArtistName()) ?></a>
                                </td>
                                <td><?= htmlspecialchars($artwork->getYearOfWork()) ?></td>
                                <td>
                                    <img src="<?= getCorrectImagePath('../assets/images/works/square-small/', ($artwork->getImageFilename())) ?>"
                                         title="<?= htmlspecialchars($artwork->getTitle()) ?>"
                                         alt="<?= htmlspecialchars($artwork->getTitle()) ?>" class="img-fluid"/></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
