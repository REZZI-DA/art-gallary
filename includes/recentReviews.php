<?php
$db = new Database();
$db->connect();

try {

    $SQL = "SELECT r.Rating, r.ReviewDate, r.Comment, c.FirstName, c.LastName, a.Title as ArtworkTitle, r.ReviewId,a.ArtworkID
            FROM reviews r
            JOIN customers c ON r.customerID = c.customerID
            JOIN artworks a ON r.artworkID = a.ArtworkID
            ORDER BY r.ReviewDate DESC
            LIMIT 3";
    $stmt = $db->prepareStatement($SQL);
    $stmt->execute();
    $threeMostReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $db->close();

 

} catch (PDOException $e) {
    $db->close();
    // Throw exception with error message if fetching reviews fails
    throw new Exception("Error fetching reviews: " . $e->getMessage());
}
?>


<section id="testimonials" >
    <div class="container">
        <h5 class="font-weight-bold text-center mb-3">Review from clients</h5>
   
        <div class="row">
            <?php if (!empty($threeMostReviews)): ?>
                    <?php foreach ($threeMostReviews as $review): ?>
                            <div class="col-sm-7">
                            <hr class="full-width-hr">
                                <div class="review-block">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <!-- <img src="http://dummyimage.com/60x60/666/ffffff&text=No+Image" class="img-rounded"> -->
                                            <a href="#" style="color: #333; pointer-events: none; cursor: default;" style="color: #333;">
                                                <?php echo htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']); ?>
                                            </a>

                                            <div class="review-block-date">
                                            <?php
                                            $reviewDate = $review['ReviewDate'];
                                            $dateOnly = date("Y-m-d", strtotime($reviewDate));
                                            echo htmlspecialchars($dateOnly);
                                            ?>
                                        </div>

                                        </div>
                                        <div class="col-sm-9">
                                            <div class="review-block-rate">
                                                <?php
                                                $rating = intval($review['Rating']);
                                                for ($i = 1; $i <= 5; $i++): ?>
                                                        <?php if ($i <= $rating): ?>
                                                                <i class="bi bi-star-fill stars"></i>
                                                        <?php else: ?>
                                                                <i class="bi bi-star stars"></i>
                                                        <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                           

                                                <a class="review-block-title" href="/art-library/pages/artwork.php?id=<?= htmlspecialchars($review['ArtworkID']) ?>" >
                <?php echo htmlspecialchars($review['ArtworkTitle']); ?>
            </a>
                                                        <div class="review-block-description">
                                <?php
                                $comment = $review['Comment'];
                                // Begrenzen auf maximal 100 Zeichen
                                if (strlen($comment) > 100) {
                                    $comment = substr($comment, 0, 220); // Maximal 100 Zeichen
                                    $comment = substr($comment, 0, strrpos($comment, ' ')); // Letztes vollständiges Wort
                                    echo $comment . '...'; // Füge Ellipse hinzu, um abgekürztes Ende anzuzeigen
                                } else {
                                    echo $comment;
                                }
                                ?>
                        </div>
                                 </div>
                                    </div>
                                </div>
             
                            </div>
                    <?php endforeach; ?>
          
            <?php endif; ?>
        </div>
    </div>
</section>

