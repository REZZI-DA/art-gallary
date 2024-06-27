<?php
$isAdmin = isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] === true;
$isUser = isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true;

$fileName = basename(__FILE__);
$folderName = basename(dirname(__FILE__));
$parentFolderName = basename(dirname(dirname(__FILE__)));


if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];



if ($isAdmin || $isUser) {
    $userInfo = $_SESSION["userInfo"];
    $user = $_SESSION["user"];
    $userID = $userInfo->getCustomerID();


}
if (isset($_POST['ReviewId']) && $isAdmin && !isset($_POST['rating'])&& !isset($_POST['comment'])) {


    $db = new Database();
    $db->connect();
    $sql = "DELETE FROM `reviews` 
            WHERE `reviews`.`ReviewId` = :reviewID";
    $stmt = $db->prepareStatement($sql);
    $stmt->bindParam(':reviewID', $_POST['ReviewId'], PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetch();
    echo '<script type="text/javascript">
           window.location = "'.$uri.'/art-library/pages/artwork.php?id=' . $_GET['id'] . '"
      </script>';

    exit;

}

if (isset($_POST['artworkID'], $_POST['comment'], $_POST['rating']) && ($isAdmin || $isUser)) {

    $comment =  htmlspecialchars($_POST['comment']);
    if($comment != null && $comment != "")
    {
        $rating = $_POST['rating'];
        if($rating > 5){$rating = 5;}
        if($rating < 1){$rating = 1;}
        $artworkId = $_POST['artworkID'];

        $customerID = $userInfo->getCustomerID();
        $date = date('Y-m-d');

        $db = new Database();
        $db->connect();

        $SQL = "SELECT * FROM `reviews` WHERE ArtWorkId = :artworkID AND CustomerID = :customerID";
        $stmt = $db->prepareStatement($SQL);
        $stmt->bindParam(':artworkID', $artworkId, PDO::PARAM_INT);
        $stmt->bindParam(':customerID', $customerID, PDO::PARAM_INT);
        $stmt->execute();
        $availableUserReview = $stmt->fetch();

        if ($availableUserReview != null) {
            $sql = "UPDATE `reviews` SET `ArtWorkId`= :artworkId,`CustomerId`=:customerId,`ReviewDate`=:reviewDate,`Rating`=:rating,`Comment`= :comment WHERE `ReviewId`= :reviewId";
            $stmt = $db->prepareStatement($sql);
            $stmt->bindParam(':reviewId', $availableUserReview['ReviewId'], PDO::PARAM_INT);
        } else {

            $sql = "INSERT INTO `reviews` (`ReviewId`, `ArtWorkId`, `CustomerId`, `ReviewDate`, `Rating`, `Comment`)
            VALUES( NULL, :artworkId , :customerId , :reviewDate , :rating , :comment  )";
            $stmt = $db->prepareStatement($sql);
        }


        $stmt->bindParam(':artworkId', $artworkId, PDO::PARAM_INT);
        $stmt->bindParam(':customerId', $customerID, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':reviewDate', $date, PDO::PARAM_STR);
        $stmt->execute();


        $db->close();


        $isSaved = true;
        echo '<script type="text/javascript">
              window.location = "'.$uri.'/art-library/pages/artwork.php?id=' . $_GET['id'] . '"
          </script>';

        exit;
    }
}


$db = new Database();
$artworkID = isset($_GET['id']) ? intval($_GET['id']) : 1;

if ($artworkID === 0) {
    echo "Invalid Genre-ID."; // make it to 404 error

    exit;
}


if ($isAdmin || $isUser) {
    $db->connect();


    $sql = "SELECT r.Rating , r.ReviewDate ,r.Comment,c.FirstName,c.LastName,c.City,c.Country,r.ReviewId
       
       
FROM reviews r
JOIN customers c ON r.customerID = c.customerID
WHERE r.artworkID = :artworkID && c.CustomerID != :userID
ORDER BY ReviewDate DESC";

    $stmt = $db->prepareStatement($sql);
    $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll();

    $sql = "SELECT r.ReviewId ,r.Rating , r.ReviewDate ,r.Comment,c.FirstName,c.LastName
       
       
FROM reviews r
JOIN customers c ON r.customerID = c.customerID
WHERE r.artworkID = :artworkID && c.CustomerID = :userID
";

    $stmt = $db->prepareStatement($sql);
    $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $userReview = $stmt->fetch();
    $db->close();
    if ($userReview != null) {
        $rating = $userReview['Rating'];
    }

} else {
    $db->connect();


    $sql = "SELECT r.Rating , r.ReviewDate ,r.Comment,c.FirstName,c.LastName,c.City,c.Country,r.ReviewId
       
       
FROM reviews r
JOIN customers c ON r.customerID = c.customerID
WHERE r.artworkID = :artworkID 
ORDER BY ReviewDate DESC";

    $stmt = $db->prepareStatement($sql);
    $stmt->bindParam(':artworkID', $artworkID, PDO::PARAM_INT);

    $stmt->execute();
    $reviews = $stmt->fetchAll();
}
?>


<link rel="stylesheet" href="/<?php echo $parentFolderName; ?>/assets/css/review.css">

<div class="reviewContainer" style="margin-left: 100px;">
    <?php if ($isAdmin || $isUser): ?>
        <div class="reviewForm">
            <div class="container" >
                <?php if ($isAdmin && $userReview != null): ?>
                    <form method="post" id="<?= htmlspecialchars($userReview['ReviewId']) ?>">
                        <input style="display: none; margin-bottom: 20px;" id="ReviewId" name="ReviewId" value="<?= htmlspecialchars($userReview['ReviewId']) ?>"/>
                        <label for="ReviewId" title="ReviewId"></label>
                        <button class="btn" type="button" onclick="confirmation(<?= htmlspecialchars($userReview['ReviewId']) ?>)" style="text-align: end;margin-left: 380px;z-index: 99;">
                            <i style="font-size: 20px;color: red;" class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                <?php endif ?>

                <form method="post" >
                    <div class="row">
                        <div class="col-10">
                            <div class="rating">
                                <?php if ($userReview != null): ?>
                                    <?php for ($i = 5; $i > 0; $i--): ?>
                                        <input id="star<?= $i ?>" name="rating" type="radio" value="<?= $i ?>" <?= ($i == $rating) ? 'checked' : '' ?> />
                                        <label for="star<?= $i ?>" title="<?= $i ?> Stars">☆</label>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <?php for ($i = 5; $i > 0; $i--): ?>
                                        <input id="star<?= $i ?>" name="rating" type="radio" value="<?= $i ?>" />
                                        <label for="star<?= $i ?>" title="<?= $i ?> Stars">☆</label>
                                    <?php endfor; ?>
                                <?php endif; ?>
                            </div>
                            <div class="headline">
                                <strong style="font-size: 18px;"> YOU</strong>
                                <p style="font-size: 8px;margin-top: 2px;">Reviewed on <?php echo date("F d, Y"); ?></p>
                            </div>
                            <br>
                            <input style="display: none;margin-bottom: 20px;" id="artworkID" name="artworkID" value="<?= htmlspecialchars($artworkID) ?>"/>
                            <label for="artworkID" title="artwork"></label>
                            <textarea class="commentInput" draggable="false" name="comment"
                                      placeholder="Here you can write your comment ..." required
                                      id="comment"><?php if ($userReview != null) {echo htmlspecialchars($userReview['Comment']);
                                } ?></textarea>
                            <label for="comment" title="comment"></label>
                        </div>
                        <div class="col-2" style="text-align: center;margin-top: 10px;">
                            <br>
                            <?php if ($userReview != null): ?>
                                <div style="height: 80px;margin-bottom:50px;">
                                    <div id="saved" style="margin-left:0px;display:block;color:greenyellow;margin-bottom:00px; padding-bottom: 5px;padding-top:48px; text-align: center;">
                                        <i style="font-size: 40px;" class="fa-solid fa-check"></i><br>
                                        <p>Saved</p>
                                    </div>
                                    <button id="edit" class="btn" style="margin-left:0px;display:none;margin-bottom:00px; padding-bottom: 5px;margin-top:50px; text-align: center;">
                                        <i style="font-size: 20px;" class="fa-solid fa-pencil"></i></button>
                                </div>
                                <input style="display: none;margin-bottom: 20px;" id="ReviewId" name="ReviewId" value="<?= htmlspecialchars($userReview['ReviewId']) ?>"/>
                                <label for="reviewId" title="review"></label>
                            <?php else: ?>
                                <button id="button" class="btn" style="margin-left:10px;display:block;margin-bottom:10px; padding-bottom: 18px;padding-top:28px; text-align: center;">
                                    <strong style="font-size: 50px; ">+</strong></button>
                                <br>
                            <?php endif ?>
                            <br>
                            <script>
                                window.onload = function () {
                                    setTimeout(function () {
                                        document.getElementById('saved').style.display = 'none';
                                        document.getElementById('edit').style.display = 'block';
                                    }, 5000);
                                };
                            </script>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($reviews as $review): ?>
        <div class="review-border">
            <div class="readOnlyRating">
                <?php if ($isAdmin): ?>
                    <form method="post" id="deleteForm_<?= htmlspecialchars($review['ReviewId']) ?>">
                        <input type="hidden" name="ReviewId" value="<?= htmlspecialchars($review['ReviewId']) ?>">
                        <button class="btn" type="button" onclick="confirmation('deleteForm_<?= htmlspecialchars($review['ReviewId']) ?>')" style="text-align: end; margin-left: 1rem;">
                            <i style="font-size: 20px; color: red;" class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                <?php endif ?>
                <?php for ($x = 5 - $review['Rating']; $x > 0; $x--): ?>
                    <label>☆</label>
                <?php endfor; ?>
                <?php for ($x = 0; $x < $review['Rating']; $x++): ?>
                    <label>★</label>
                <?php endfor; ?>
            </div>
            <div class="headline">
                <strong style="font-size: 18px;"><?= htmlspecialchars($review['FirstName']) ?>  <?= htmlspecialchars($review['LastName']) ?>
                    <mark style="margin-left3px;font-size: 8px;background-color:white;margin-bottom: 3px;">
                        from <?= htmlspecialchars($review['City']) ?>(<?= htmlspecialchars($review['Country']) ?>)
                    </mark>
                </strong>
                <p style="font-size: 8px;margin-top: 2px;">Reviewed on <?php echo date('F d, Y', strtotime($review['ReviewDate'])); ?></p>
            </div>
            <div style="overflow: auto; max-height: 140px;white-space: normal">
                <?= htmlspecialchars($review['Comment']) ?>
            </div>
        </div>
    <?php endforeach; ?>
    <script>
        function confirmation(formId) {
            var r = confirm("Sind Sie sicher, dass Sie fortfahren möchten?");
            if (r == true) {
                document.getElementById(formId).submit();
            }
        }
    </script>
</div>


