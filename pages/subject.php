<?php
require_once('../config/database.php');
require_once('../includes/functions.php');

$db = new Database();
$db->connect();

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];

$subjectID = isset($_GET['id']) ? intval($_GET['id']) : 0;



// Define sorting parameters
$sortOptions = ['title', 'artistName', 'yearofwork'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortOptions) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$nextOrder = $order === 'ASC' ? 'DESC' : 'ASC';

// Secondary sorting for consistent display
$secondarySort = $sort === 'artistName' ? 'title' : ($sort === 'title' ? 'yearofwork' : 'title');

// Query to select the subject details
$sqlSubject = "
SELECT s.subjectID, s.subjectName
FROM subjects s
WHERE s.subjectID = :subjectID";

$stmtSubject = $db->prepareStatement($sqlSubject);
$stmtSubject->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
$stmtSubject->execute();
$subject = $stmtSubject->fetch();

if ($subjectID === 0) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

if (!$subject) {
    header('Location:'.$uri.'/art-library/pages/error.php');
    exit;
}

// Query to select artworks based on the subject with sorting
$sqlArtworks = "
SELECT a.artworkID, a.title, a.imagefilename, 
       b.artistID,
       CASE 
           WHEN b.firstName IS NULL AND b.lastName IS NULL THEN 'Artist is Unknown'
           WHEN b.firstName IS NULL THEN b.lastName
           WHEN b.lastName IS NULL THEN b.firstName
           ELSE CONCAT(b.firstName, ' ', b.lastName) 
       END AS artistName,
       a.yearofwork
FROM artworks a
JOIN ArtWorkSubjects aws ON a.artworkID = aws.ArtWorkID
LEFT JOIN artists b ON a.artistID = b.artistID
WHERE aws.SubjectID = :subjectID
ORDER BY $sort $order, $secondarySort ASC";

$stmtArtworks = $db->prepareStatement($sqlArtworks);
$stmtArtworks->bindParam(':subjectID', $subjectID, PDO::PARAM_INT);
$stmtArtworks->execute();
$relatedArtworks = $stmtArtworks->fetchAll(PDO::FETCH_ASSOC);

$db->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<title><?= htmlspecialchars($subject['subjectName']) ?> Subject</title>
<div id="content" class="p-4 p-md-5">

    <div class="container light-style flex-grow-1 container-p-y" style="padding: 1rem; margin:1rem">
        <link rel="stylesheet" href="../assets/css/tables.css">

        <div class="container">
            <h1 class="table-title">Artworks Related to <?= htmlspecialchars($subject['subjectName']) ?></h1>

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th class="yellow" style="width: 45%">
                        <a class="white-link" href="?id=<?= $subjectID ?>&sort=title&order=<?= $sort === 'title' ? $nextOrder : 'ASC' ?>">Title <?= $sort === 'title' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                    </th>
                    <th class="yellow" style="width: 30%">
                        <a class="white-link" href="?id=<?= $subjectID ?>&sort=artistName&order=<?= $sort === 'artistName' ? $nextOrder : 'ASC' ?>">Artist <?= $sort === 'artistName' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                    </th>
                    <th class="yellow" style="width: 20%">
                        <a class="white-link" href="?id=<?= $subjectID ?>&sort=yearofwork&order=<?= $sort === 'yearofwork' ? $nextOrder : 'ASC' ?>">Year <?= $sort === 'yearofwork' ? ($order === 'ASC' ? '▲' : '▼') : '' ?></a>
                    </th>
                    <th class="yellow" style="width: 5%">Image</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($relatedArtworks as $artwork): ?>
                    <tr onclick="window.location='artwork.php?id=<?= $artwork['artworkID'] ?>';">
                        <td>
                            <a href="artwork.php?id=<?= $artwork['artworkID'] ?>"><?= htmlspecialchars($artwork['title']) ?></a>
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
                            <img src="<?= getCorrectImagePath('../assets/images/works/square-small/', $artwork['imagefilename']) ?>"
                                 title="<?= htmlspecialchars($artwork['title']) ?>"
                                 alt="<?= htmlspecialchars($artwork['title']) ?>" class="img-fluid"/>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- "Go Back to Subjects" Button -->
            <div class="text-center mt-4">
                <a href="subjects.php" class="btn btn-secondary">Back to Subjects</a>
            </div>
        </div>
    </div>

</div>
</div>
<?php include '../includes/footer.php'; ?>
