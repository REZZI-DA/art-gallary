<?php
require_once('../repositories/genreRepository.php');
require_once('../includes/functions.php');

$db = new Database();
$repository = new GenreRepository($db);

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];

$genreID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($genreID === 0) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

try {
    // Fetch genre details by ID
    $genre = $repository->getGenreById($genreID);
    if (!$genre) {
        header('Location:'.$uri.'/art-library/pages/error.php');
        exit;
    }

    // Retrieve artists associated with the genre
    $artists = $repository->getArtistsOfGenre($genreID);
    if (!$artists) {
        header('Location:'.$uri.'/art-library/pages/error.php');
        exit;
    }

    // Retrieve number of artworks associated with the genre
    $numberOfArtworks = $repository->getNumberOfArtworksByGenreID($genreID);
    if (!$numberOfArtworks) {
        header('Location:'.$uri.'/art-library/pages/error.php');
        exit;
    }

} catch (Exception $e) {
    echo htmlspecialchars($e->getMessage());
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

if (!$genreID) {
    header('Location:'.$uri.'/art-library/pages/error.php');
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
<div class="main">
    <div id="content" class="p-4 p-md-5">
        <div class="container light-style flex-grow-1 container-p-y" style="padding: 1rem; margin: 1rem;">
            <link rel="stylesheet" href="../assets/css/tables.css">

            <div style="justify-content: center;
    display: flex;
    margin: auto;

    flex-wrap: wrap;">
                <h1 class="table-title"> <?= htmlspecialchars($genre['GenreName']) ?></h1>

                <div class="container-fluid">

                    <div class="row">
                        <div class="col-4">

                            <div class="container-fluid detailContainer">
                                <div style="display: flex;justify-content: center; align-items: center;">
                                    <?php
                                    $imageFile = getCorrectImagePath('../assets/images/genres/square-medium/', $genreID);
                                    if (file_exists($imageFile)) {
                                        echo '<img style="min-width: 100px;min-height: 100px;max-height: 200px;max-width: 200px;" src="' . $imageFile . '" alt="Bild für ID ' . $genreID . '">';
                                    }
                                    ?>

                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-6">
                                        <strong>Era:</strong>
                                    </div>
                                    <div class="col-6">
                                        <?= htmlspecialchars($genre['Era']) ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Number of artworks*</strong>
                                    </div>
                                    <div class="col-6">
                                        <?= htmlspecialchars($numberOfArtworks['Count(*)']) ?>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">

                                    <strong>Artists </strong>
                                    <div class="col-2">

                                    </div>
                                    <div class="col-10">
                                        <?php foreach ($artists as $artist): ?>
                                            <p style="margin-top: 5px; color:black;"> <?= htmlspecialchars($artist['firstName']) ?> <?= htmlspecialchars($artist['lastName']) ?> </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <hr>
                                <div style="text-align: center">
                                    <a href="<?= htmlspecialchars($genre['Link']) ?>">Wikipedia</a>
                                    <p style="font-size:9px">*at Art Gallery</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-8">
                        <p> <?php echo $genre['Description'] ?></p>
                        <div class="container light-style flex-grow-1 container-p-y" style="padding: 0rem; margin:0rem">
                            <link rel="stylesheet" href="../assets/css/tables.css">

                            <div class="container" style=" text-align: center; margin: 0px;padding:0px; ">




                                <table class="table table-bordered table-hover" >
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


                </div>
            </div>

        </div>
    </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


