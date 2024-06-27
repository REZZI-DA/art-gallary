<?php
require_once('../config/database.php');
require_once('../repositories/artistRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$artistRepo = new ArtistRepository($db);

// Define sorting parameters
$sortOptions = ['firstName', 'yearOfBirth'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'firstName';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';

// Define secondary sorting
$secondarySort = $sort === 'firstName' ? 'lastName' : 'firstName';

try {
    // Retrieve all artists with specified sorting
    $artists = $artistRepo->getAllArtists($sort, $order, $secondarySort);
} catch (Exception $e) {
    echo "Error fetching artists: " . htmlspecialchars($e->getMessage());
    exit;
}
?>


<?php include '../includes/header.php'; ?>
        <div id="content" class="p-4 p-md-5">

            <div class="container light-style flex-grow-1 container-p-y" style=" padding: 1rem; margin:1rem">
                <link rel="stylesheet" href="../assets/css/tables.css">

                <div class="container">
                    <h1 class="table-title">Browse Artists</h1>

                        <table class="table table-bordered table-hover table-artists">
                            <thead>
                            <tr>
                                <th class="yellow" style="width: 75%">
                                    <a class="white-link" href="?sort=firstName&order=<?= $sort === 'firstName' ? $nextOrder : 'ASC' ?>">Name <?= $sort === 'firstName' ? ($order === 'ASC' ? '▲' : '▼') : '' ?>  </a>
                                     </th>
                                <th class="yellow" style="width: 20%">
                                    <a class="white-link" href="?sort=yearOfBirth&order=<?= $sort === 'yearOfBirth' ? $nextOrder : 'ASC' ?>">Year
                                        of
                                        Birth <?= $sort === 'yearOfBirth' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                                </th>
                                <th class="yellow" style="width: 5%">Image</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($artists as $artist): ?>
                                <tr onclick="window.location='artist.php?id=<?= $artist->getArtistID() ?>';">
                                    <td>
                                        <a href="artist.php?id=<?= $artist->getArtistID() ?>"><?= htmlspecialchars($artist->getFirstName()) ?> <?= htmlspecialchars($artist->getLastName()) ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($artist->getYearOfBirth()) ?></td>
                                    <td>
                                        <img src="<?= getCorrectImagePath('../assets/images/artists/square-thumb/', $artist->getArtistID()) ?>"
                                             title="<?= htmlspecialchars($artist->getFirstName()) ?> <?= htmlspecialchars($artist->getLastName()) ?>"
                                             alt="<?= htmlspecialchars($artist->getFirstName()) ?> <?= htmlspecialchars($artist->getLastName()) ?>"
                                             class="img-fluid"></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                </div>
            </div>
        </div>

<?php include '../includes/footer.php'; ?>