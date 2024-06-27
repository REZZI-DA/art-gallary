<?php
require_once('../repositories/genreRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$repository = new GenreRepository($db);

try {
    // Fetch all genres
    $genres = $repository->getAllGenres();
    if (!$genres) {
        echo "Genres not found.";
        exit;
    }
} catch (Exception $e) {
    echo htmlspecialchars($e->getMessage());
    exit;
}
?>


<?php include __DIR__ . '/../includes/header.php'; ?>
<title>Genres</title>
<div id="content" class="p-4 p-md-5">
    <div class="container light-style flex-grow-1 container-p-y" style=" padding: 1rem; margin:1rem">
        <link rel="stylesheet" href="../assets/css/tables.css">
        <div class="container">
            <h1 class="table-title">Browse Genres</h1>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="yellow" style="width: 3%">Era</th>
                        <th class="yellow" style="width: 92%">Name</th>
                        <th class="yellow" style="width: 5%">Image</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($genres as $genre): ?>

                        <tr onclick="window.location='genre.php?id=<?= htmlspecialchars($genre['GenreID']) ?>';">

                            <td><?= htmlspecialchars($genre['Era']) ?></td>
                            <td><a href="genre.php?id=<?= htmlspecialchars($genre['GenreID']) ?>"><?= htmlspecialchars($genre['GenreName']) ?></a></td>
                            <td>
                                <?php
                                $imageFile = getCorrectImagePath('../assets/images/genres/square-thumbs/', $genre['GenreID']);
                                if (file_exists($imageFile)) {
                                    echo '<img src="' . htmlspecialchars($imageFile) . '" alt="Image for ' . htmlspecialchars($genre['GenreName']) . '">';
                                } else {
                                    echo '<img src="../assets/images/genres/square-thumbs/default.jpg" alt="Default Image">';
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

<?php include '../includes/footer.php'; ?>
