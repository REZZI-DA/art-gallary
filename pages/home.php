<?php

include __DIR__ . '/../repositories/userRepository.php';
include __DIR__ . '/../config/database.php';
include __DIR__ . '/../repositories/artworkRepository.php';


$db = new Database();
$artworkRepo = new ArtworkRepository($db);

$imageDirectory = '../assets/images/works/large/';


$files = glob($imageDirectory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

$randomFiles = array_rand($files, 20);


$imageNames = [];


foreach ($randomFiles as $fileIndex) {
  $imagePath = $files[$fileIndex];
  $imageNames[] = pathinfo($imagePath, PATHINFO_FILENAME);
}

try {

  $artworks = $artworkRepo->getThreeRandomArtworks($imageNames);

  $randomArtwork1 = $artworks[0];
  $randomArtwork2 = $artworks[1];
  $randomArtwork3 = $artworks[2];
  $name = $randomArtwork1->getImageFilename();

} catch (Exception $e) {
  echo "Error fetching artworks: " . htmlspecialchars($e->getMessage());
  exit;
}

$parentFolderName = basename(dirname(dirname(__FILE__)));
?>





<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="main">
<div id="content" class="p-4 p-md-5">
        <div class="container_home">
            <h2 class="my-5 text-center">Welcome to Gallery</h2>

            <div class="d-flex carousel-nav">
                <?php for ($i = 0; $i < count($artworks); $i++): ?>
                    <a class="col<?= ($i === 0) ? ' active' : '' ?>" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $i ?>">
                        <?= $artworks[$i]->getArtistName() ?>
                    </a>
                <?php endfor; ?>
            </div>

            <div id="carouselExampleIndicators" class="carousel slide mt-4" data-bs-ride="carousel" data-bs-interval="6000">
                <div class="carousel-inner">
                    <?php for ($i = 0; $i < count($artworks); $i++): ?>
                        <div class="carousel-item<?= ($i === 0) ? ' active' : '' ?>">
                            <div class="media-29101">
                                <div class="img">
                                    <img src="<?= getCorrectImagePath('../assets/images/works/medium/', $artworks[$i]->getImageFilename()) ?>" title="<?= htmlspecialchars($artworks[$i]->getTitle()) ?>" alt="<?= htmlspecialchars($artworks[$i]->getTitle()) ?>" class="img-fluid" />
                                </div>
                                <div class="text">
                                    <a class="category d-block mb-4" href="/art-library/pages/artist.php?id=<?= htmlspecialchars($artworks[$i]->getArtistID()) ?>">
                                        By <?= htmlspecialchars($artworks[$i]->getArtistName()) ?>
                                    </a>
                                    <h2>
                                        <a href="/art-library/pages/artwork.php?id=<?= htmlspecialchars($artworks[$i]->getArtworkID()) ?>">
                                            <?= nl2br(htmlspecialchars($artworks[$i]->getTitle())) ?>
                                        </a>
                                    </h2>
                                    <?php
                                    $description = $artworks[$i]->getDescription();
                                    if (strlen($description) > 1100) {
                                        $description = substr($description, 0, 1100) . "...";
                                    }
                                    ?>
                                    <p><?= nl2br($description) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <a class="carousel-control-prev" type="" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon custom-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" type="" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon custom-icon" aria-hidden="true" style="color: #ffc107;"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
        </div>
        <hr />
    </div>
    <?php include __DIR__ . '/../includes/artworksmostreviewed.php'; ?>
    <?php include __DIR__ . '/../includes/mostreviewedartists.php'; ?>
    <?php include __DIR__ . '/../includes/recentReviews.php'; ?>

</div>

</div>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    var carousel = document.getElementById('carouselExampleIndicators');
    var navLinks = document.querySelectorAll('.carousel-nav .col');

    carousel.addEventListener('slide.bs.carousel', function (event) {
        var activeIndex = event.to;
        
        navLinks.forEach(function(link, index) {
            if (index === activeIndex) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
});
    </script>
<?php include __DIR__ . '/../includes/footer.php'; ?>