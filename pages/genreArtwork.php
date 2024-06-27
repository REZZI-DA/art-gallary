<?php
require_once('../includes/session.php');
require_once('../repositories/genreRepository.php');
require_once('../includes/functions.php');
require_once('../config/database.php');

$db = new Database();
$repository = new GenreRepository($db);

$genreID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($genreID === 0) {
    http_response_code(404);
    echo "Invalid Genre-ID.";
    exit;
}

try {
    // Fetch genre details by ID
    $genre = $repository->getGenreById($genreID);
    if (!$genre) {
        http_response_code(404);
        echo "Genre not found.";
        exit;
    }
} catch (Exception $e) {
    echo htmlspecialchars($e->getMessage());
    exit;
}

$db->connect();

$sortOptions = ['title', 'artistName', 'yearofwork'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'ASC';
$order = $order === 'DESC' ? 'DESC' : 'ASC';
$nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';
$secondarySort = $sort === 'artistName' ? 'title' : ($sort === 'title' ? 'yearofwork' : 'title');

// SQL query to retrieve artworks based on genre ID and sorting criteria
$sql = "SELECT a.artworkID, a.title, 
               b.artistID,
               CASE 
                   WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Artist is Unknown'
                   WHEN b.firstName IS NULL THEN b.lastName
                   WHEN b.lastName IS NULL THEN b.firstName
                   ELSE CONCAT(b.firstName, ' ', b.lastName) 
               END AS artistName,
               a.yearofwork, a.imagefilename 
        FROM artworks a
        JOIN artists b ON a.artistID = b.artistID
        JOIN artworkgenres g ON a.ArtWorkID = g.artworkID
        WHERE g.GenreID = :genreId
        ORDER BY " . ($sort === 'artistName' ? "artistName" : "a.$sort") . " $order, $secondarySort $order";

$stmt = $db->prepareStatement($sql);
$stmt->bindParam(':genreId', $genreID, PDO::PARAM_INT);
$stmt->execute();
$artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->close();
?>


<?php include __DIR__ . '/../includes/header.php'; ?>
<title><?= htmlspecialchars($genre['GenreName']) ?></title>
<div id="content" class="p-4 p-md-5">
    <div class="container light-style flex-grow-1 container-p-y" style="padding: 1rem; margin:1rem">
        <link rel="stylesheet" href="../assets/css/tables.css">

        <div class="container">
            <h1 class="table-title text-center"><?= htmlspecialchars($genre['GenreName']) ?></h1>

            <div class="text-center">
                <a href="genre.php?id=<?= htmlspecialchars($genreID) ?>" style="font-weight: normal ;text-decoration: none; margin-right:20px; color: black;">Information</a>
                <a href="genreArtwork.php?id=<?= htmlspecialchars($genreID) ?>" style="color: black">Artworks</a>
            </div>
            <hr style="margin-top: -5px; margin-bottom: 30px;">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr >
                        <th class="yellow">
                            <a class="white-link" href="?id=<?= htmlspecialchars($genreID) ?>&sort=title&order=<?= $sort === 'title' ? $nextOrder : 'ASC' ?>" style="text-decoration: none;">
                                Title <?= $sort === 'title' ? ($order === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th class="yellow">
                            <a class="white-link" href="?id=<?= htmlspecialchars($genreID) ?>&sort=artistName&order=<?= $sort === 'artistName' ? $nextOrder : 'ASC' ?>" style="text-decoration: none;">
                                Artist <?= $sort === 'artistName' ? ($order === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th class="yellow">
                            <a class="white-link" href="?id=<?= htmlspecialchars($genreID) ?>&sort=yearofwork&order=<?= $sort === 'yearofwork' ? $nextOrder : 'ASC' ?>" style="text-decoration: none;">
                                Year <?= $sort === 'yearofwork' ? ($order === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th class="yellow">Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($artworks as $artwork): ?>
                        <tr onclick="window.location='artwork.php?id=<?= htmlspecialchars($artwork['artworkID']) ?>';">
                            <td>
                                <a href="artwork.php?id=<?= htmlspecialchars($artwork['artworkID']) ?>">
                                    <?= htmlspecialchars($artwork['title']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($artwork['artistID'])): ?>
                                    <a href="artist.php?id=<?= htmlspecialchars($artwork['artistID']) ?>">
                                        <?= htmlspecialchars($artwork['artistName']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($artwork['artistName']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($artwork['yearofwork']) ?></td>
                            <td>
                                <?php
                                $imageFile = getCorrectImagePath('../assets/images/works/square-medium/', $artwork['imagefilename']);
                                if (file_exists($imageFile)) {
                                    echo '<img src="' . htmlspecialchars($imageFile) . '" alt="' . htmlspecialchars($artwork['title']) . '" style="width:100px;height:auto;">';
                                } else {
                                    echo '<img src="../assets/images/genres/square-thumbs/default.jpg" alt="Default Image" style="width:100px;height:auto;">';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
