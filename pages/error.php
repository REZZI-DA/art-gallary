<?php

if (session_status() === PHP_SESSION_NONE) {
    require_once('../includes/session.php');
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
} else {
    $error_message = "An unknown error occurred.";
}

if (isset($_SESSION['page'])) {
    $page = $_SESSION['page'];
} else {
    $page = "home.php";
}

if (isset($_SESSION['back_to'])) {
    $back_to = $_SESSION['back_to'];
} else {
    $back_to = "Back to Home"; 
}

unset($_SESSION['error_message']);
unset($_SESSION['page']);
unset($_SESSION['back_to']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>
        We're sorry...
    </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

</head>
<body style="vertical-align: middle; justify-content: center;">

<div class="container-fluid">
    <div class="row" style="height: 1000px;">
        <div class="col-4" style="border-color: black;background-color: #f3b41b">

            <img style="width: 300px; height:300px;margin-top: 300px; float: right;"   alt="Little web-robot painting an sad Artwork" src="../assets/images/_54674a2e-999b-4b3c-a427-a6904380e833.png" >

        </div>
        <div class="col-8" style="font-size: 30px;margin-top: 350px;">
            <div style="vertical-align: center">
            <h1 style="font-size: 80px;">ERROR</h1>
            <?php echo $error_message; ?><br>
            <a class="btn" style="margin-top:30px;background-color: #f3b41b" href="<?php echo $page; ?>"><?php echo $back_to; ?></a>
            </div>
        </div>
    </div>
</div>


</body>
</html>
