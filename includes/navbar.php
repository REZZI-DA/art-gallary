<?php
// Include necessary session handling and utility functions
include __DIR__ . '/../includes/session.php';
include __DIR__ . '/../includes/functions.php';

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Determine parent folder name for logo path
$parentFolderName = basename(dirname(dirname(__FILE__)));
$logo = "/$parentFolderName/assets/images/gallery.png";

// Check if user is logged in as admin or regular user
$isAdmin = isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] === true;
$isUser = isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true;

// Handle logout if requested
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    logout();
}

// Display user information if available in session
if (isset($_SESSION['userInfo'])) {

    $userInfo = $_SESSION["userInfo"];
    $user = $_SESSION["user"];
}
?>


<section class="navbar">
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <h3 class="navbar-brand" href="index.html">
                <img src="<?php echo $logo; ?>" alt="Art Gallery Logo" class="logo">
                Art Gallery
            </h3>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <a href="<?php echo generateUrl("home.php"); ?>" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item dropdown <?php echo ($current_page == 'artists.php' || $current_page == 'artworks.php' || $current_page == 'genres.php' || $current_page == 'subjects.php') ? 'active' : ''; ?>">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Browse
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item <?php echo ($current_page == 'artists.php') ? 'active' : ''; ?>"
                                   href="<?php echo generateUrl("artists.php"); ?>">Artists</a></li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'artworks.php') ? 'active' : ''; ?>"
                                   href="<?php echo generateUrl("artworks.php"); ?>">Artworks</a></li>
                            <li><a class="dropdown-item <?php echo ($current_page == 'genres.php') ? 'active' : ''; ?>"
                                   href="<?php echo generateUrl("genres.php"); ?>">Genres</a></li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'subjects.php') ? 'active' : ''; ?>"
                                   href="<?php echo generateUrl("subjects.php"); ?>">Subjects</a></li>
                        </ul>
                    </li>
                    <li class="nav-item <?php echo ($current_page == 'advancedsearchs.php') ? 'active' : ''; ?>">
                        <a href="<?php echo generateUrl("advancedsearch.php"); ?>" class="nav-link">Advanced Search</a>
                    </li>
                    <li class="nav-item <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                        <a href="<?php echo generateUrl("about.php"); ?>" class="nav-link">About</a>
                    </li>


                </ul>

                <form action="<?php echo generateUrl("searchresults.php"); ?>" method="GET" class="searchform mx-auto">
                    <input type="hidden" name="simpleSearch" value="true">
                    <input type="text" name="searchTerm" class="form-control" placeholder="Search">
                    <button type="submit" class="btn btn-outline-light">
                        <i class="fa fa-search"></i>
                    </button>
                </form>



            </div>
            <ul class="navbar-nav ms-auto"> <!-- ms-auto hinzugefügt -->
                <li class="nav-item dropdown ms-auto" id="account"> <!-- ms-auto hinzugefügt -->
                    <a class="nav-link dropdown-toggle btn-glow" href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                    </a>

                    <ul class="dropdown-menu dropleft" aria-labelledby="userDropdown">
                        <?php if ($isUser || $isAdmin): ?>
                            <li class="dropdown-header">
                                <div style="display: flex; flex-direction: column;">
                                    <strong><?php echo htmlspecialchars($userInfo->getFirstName() . ' ' . $userInfo->getLastName()); ?></strong>
                                    <small><?php echo htmlspecialchars($user->getUserName()); ?></small>
                                </div>
                            </li>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>
                        <?php if ($isAdmin): ?>


                            <span class="badge badge-admin ml-3 d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill mr-2" style="font-size: 1.2rem;"></i> Admin
                             </span>


                            <li><a class="dropdown-item" href="<?php echo generateUrl("manageusers.php"); ?>"><i
                                            class="fas fa-cogs"></i> <span class="icon-text-ma">Manage Users</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($isUser): ?>
                            <span class="badge badge-admin ml-3 d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill mr-2" style="font-size: 1.2rem;"></i> User
                             </span>
                        <?php endif; ?>
                        <?php if ($isUser || $isAdmin): ?>
                            <li><a class="dropdown-item"
                                   href="<?php echo generateUrl("searchresults.php?favorites=true"); ?>"><i
                                            class="fas fa-user"></i> <span class="icon-text">Favorites</span></a></li>
                            <li><a class="dropdown-item" href="<?php echo generateUrl("myaccount.php"); ?>"><i
                                            class="fas fa-user"></i> <span class="icon-text">My Account</span></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="/art-library/includes/navbar.php?logout=true"><i
                                            class="fas fa-sign-out-alt"></i> <span
                                            class="icon-text">Log Out</span></a></li>
                        <?php else: ?>
                            <li class="dropdown-header">
                                <div>
                                    <i class="fas fa-lock" style="font-size:  0.8rem; color: #6c757d;"></i>
                                    <strong style="margin-top: 0.5rem;">Not logged in</strong>
                                </div>


                            </li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item"
                                   href="<?php echo generateUrl("searchresults.php?favorites=true"); ?>"><i
                                            class="fas fa-user-plus"></i> <span class="icon-text">Favorites</span></a>
                            </li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="<?php echo generateUrl("register.php"); ?>"><i
                                            class="fas fa-user-plus"></i> <span class="icon-text">Register</span></a>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo generateUrl("login.php"); ?>"><i
                                            class="fas fa-sign-in-alt"></i> <span class="icon-text">Login</span></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>


        </div>
    </nav>
    <!-- END nav -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['searchTerm'])) {
            $searchTerm = trim($_GET['searchTerm']);
            if (strlen($searchTerm) < 3) {
                $_SESSION['snackbar_message'] = "You must at least enter 3 characters";
                $_SESSION['snackbar_type'] = "error";
                $_SESSION['snackbar_duration'] = 9000;
                include '../pages/snackbar.php';
                exit;
            }
        }
    } ?>
</section>
