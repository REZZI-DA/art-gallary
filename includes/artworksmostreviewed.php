<?php

$db = new Database();
$artworkRepo = new ArtworkRepository($db);



try {

    $artworksmostreview = $artworkRepo->threemostreviewartworks();

} catch (Exception $e) {
    echo "Error fetching artworks: " . htmlspecialchars($e->getMessage());
    exit;
}

$parentFolderName = basename(dirname(dirname(__FILE__)));
?>
<div class="container_home">
  <!-- For Demo Purpose-->
  <header class="text-center mb-5">
    <h5 class="mt-5">Top Works by Reviews</h5>
    <p class="font-italic text-muted mb-0">An awesome Bootstrap 4 cards collection with variant content.</p>
  </header>

  <!-- Artwork Cards Row -->
  <div class="row justify-content-center">
    <?php foreach ($artworksmostreview as $artwork): ?>
      <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 mt-4">
        <!-- Card-->
        <div class="card rounded shadow-sm border-0">
          <div class="card-body p-4">
            <div class="image-container">
              <img src="<?= getCorrectImagePath('../assets/images/works/medium/', ($artwork['imagefilename'])) ?>" 
                title="<?= htmlspecialchars($artwork['title']) ?>" 
                alt="<?= htmlspecialchars($artwork['title']) ?>" 
                class="img-fluid d-block mx-auto mb-3 artwork-image" />
            </div>
            <h5>
              <a class="review-block-title" href="/art-library/pages/artwork.php?id=<?= htmlspecialchars($artwork['artworkID']) ?>" style="color: #333;">
                <?= htmlspecialchars($artwork['title']); ?>
              </a>
            </h5>
            <p class="small text-muted font-italic"><?= htmlspecialchars($artwork['originalHome']) ?></p>
            <ul class="list-inline small">
              <div class="list-inline-item m-0">
                <?php
                $averageRating = $artwork['AverageRating'];
                $roundedRating = round($averageRating * 2) / 2;

                for ($x = 1; $x <= 5; $x++): ?>
                  <?php if ($x <= $roundedRating): ?>
                    <i class="bi bi-star-fill stars"></i>
                  <?php elseif ($x - 0.5 == $roundedRating): ?>
                    <i class="bi bi-star-half stars"></i>
                  <?php else: ?>
                    <i class="bi bi-star stars"></i>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
            </ul>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>