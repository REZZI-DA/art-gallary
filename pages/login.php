<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../repositories/userRepository.php');

$fileName = basename(__FILE__);
$folderName = basename(dirname(__FILE__));
$parentFolderName = basename(dirname(dirname(__FILE__)));
$registerUrl = "/$parentFolderName/$folderName/register.php";

session_start();

// Check if login form was submitted
if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $repo = new userRepository(new Database());
    if ($repo->verifyUserLogin($username, $password)) {
        // Login successful
        $user = $repo->getUser($username);
        $id = $user->getCustomerID();
        $userInfo = $repo->getUserbyId($id);

        if ($user->getType() == 1) { // Admin
            $_SESSION['userInfo'] = $userInfo;
            $_SESSION['user'] = $user;
            $_SESSION['adminlogin'] = true;
            header("Location: /$parentFolderName/");
            exit;
        } elseif ($user->getType() == 0) { // Regular user
            $_SESSION['userInfo'] = $userInfo;
            $_SESSION['user'] = $user;
            $_SESSION['userlogin'] = true;
            header("Location: /$parentFolderName/");
            exit;
        }
    } else {
        // Incorrect credentials
        $_SESSION['snackbar_message'] = "Incorrect login credentials. Please try again.";
        $_SESSION['snackbar_type'] = "error";
        $_SESSION['snackbar_duration'] = 4000;
        header("Location: /$parentFolderName/$folderName/login.php"); // Optional: Redirect back to login page on error
        exit;
    }
}
?>



<?php include '../includes/header.php'; ?>
<div >

   
<div id="content" class="p-4 p-md-5">
        <section class="ftco-section_log">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-10">
                        <div class="wrap d-md-flex">
                            <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
                                <div class="text w-100">
                                    <h2>Welcome to login</h2>
                                    <p>Don't have an account?</p>
                                    <a href="<?php echo $registerUrl; ?>" class="btn btn-white btn-white">Sign Up</a>
                                </div>
                            </div>
                            <div class="login-wrap p-4 p-lg-5">
                                <div class="d-flex">
                                    <div class="w-100">
                                        <h3 class="mb-4">Sign In</h3>
                                    </div>
                                </div>
                                <form class="signin-form" method="POST">
                                    <div class="form-group mb-3">
                                        <label class="label" for="username">Username</label>
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label" for="password">Password</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                    </div>
                                    <div class="form-group d-flex align-items-center justify-content-between">
                                        <div>
                                            <button type="submit" name="submit" class="form-control-log btn btn-primary submit px-4">Sign In</button>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input custom-checkbox" id="rememberMe" checked>
                                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                                        </div>
                                    </div>
									<?php include 'snackbar.php'; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

