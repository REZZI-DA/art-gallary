<?php
// Include necessary files for database connection and functions
require_once('../config/database.php');
require_once('../includes/functions.php');

// Create a new instance of the Database class and connect to the database
$db = new Database();
$db->connect();

// SQL query to fetch subjects from the database
$sql = "
SELECT s.subjectId, s.subjectName
FROM subjects s";

// Prepare and execute the SQL statement
$stmt = $db->prepareStatement($sql);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results as associative array

// Close the database connection
$db->close();

// If no subjects are found, output a message and exit the script
if (!$subjects) {
    echo "No Subjects found";
    exit;
}

/**
 * Comparison function to sort subjects by name.
 *
 * @param array $a First subject array.
 * @param array $b Second subject array.
 * @return int Comparison result (-1, 0, or 1).
 */
function compareSubjectsByName($a, $b)
{
    return strcmp($a['subjectName'], $b['subjectName']);
}

// Sort the subjects array alphabetically by 'subjectName'
usort($subjects, 'compareSubjectsByName');
?>


<?php include '../includes/header.php'; ?>

<div id="content" class="p-4 p-md-5">
    <div class="container light-style flex-grow-1 container-p-y" style=" padding: 1rem; margin:1rem">
        <link rel="stylesheet" href="../assets/css/tables.css">
        <div class="container">
            <h1 class="table-title">Browse Subjects</h1>

                <table border="1" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="yellow" style="width: 95%">Subject</th>
                        <th class="yellow" style="width: 5%">Image</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr onclick="window.location='subject.php?id=<?= htmlspecialchars($subject['subjectId']) ?>';">
                            <td>
                                <a href="subject.php?id=<?= htmlspecialchars($subject['subjectId']) ?>">
                                    <?= htmlspecialchars($subject['subjectName']) ?>
                                </a>
                            </td>
                            <?php $imagePath = getCorrectImagePath('../assets/images/subjects/square-thumbs/', $subject['subjectId']); ?>
                            <td>
                                <img src="<?= htmlspecialchars($imagePath) ?>"
                                     title="<?= htmlspecialchars($subject['subjectName']) ?>"
                                     alt="<?= htmlspecialchars($subject['subjectName']) ?>"/>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
       
        </div>
    </div>
    </div>
    <?php include '../includes/footer.php'; ?>
