
<?php
require_once('../includes/functions.php');
require_once (__DIR__ . '/../config/database.php');
require_once (__DIR__ . '/../repositories/userRepository.php');

include __DIR__ . '/../includes/functions.php';
session_start();
$countries = file_get_contents('https://cdn.jsdelivr.net/npm/countries-list/dist/countries.min.json');
// Check if user is logged in as admin or regular user
$isAdmin = isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] === true;
$isUser = isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true;

$fileName = basename(__FILE__);
$folderName = basename(dirname(__FILE__));
$parentFolderName = basename(dirname(dirname(__FILE__)));

// Check if user information is set in session
if (isset($_SESSION['userInfo'])) {
    $userInfo = $_SESSION["userInfo"];
    $user = $_SESSION["user"];
} else {
 
     $_SESSION['error_message'] =  "You are not logged in. Please log in.";
    $_SESSION['page'] = 'login.php';
    $_SESSION['back_to']='Go to Login';
     header("Location: error.php");
     exit;
    // header("Location: /$parentFolderName/$folderName/myaccount.php");
    // exit;
}

// Handle POST request to save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {

    // Handle password update logic
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $repeatNewPassword = $_POST['repeatNewPassword'];
    $is_validPass = validatePassword($newPassword);
echo  $repeatNewPassword ;
echo  $newPassword ;
    if (!empty($newPassword) && !empty($repeatNewPassword)) {

        if ($newPassword === $repeatNewPassword) {
            if ($is_validPass) {

                // Verify current password and update if correct
                if (password_verify($currentPassword, $user->getPass())) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);
                    $data1 = array(
                        'CustomerID' => $user->getCustomerID(),
                        'Pass' => $hashedPassword,
                        'DateLastModified' => date('Y-m-d H:i:s')
                    );

                    // Create customer login object and update repository
                    $Customerlogon = new Customerlogin($data1);
                    $repo = new userRepository(new Database());

                    if ($repo->updateCustomerlogon($Customerlogon)) {

                        $_SESSION['snackbar_message'] = "Password has been updated successfully.";
                        $_SESSION['snackbar_type'] = "success";
                        $_SESSION['snackbar_duration'] = 5000;
                        $_SESSION["user"] = $repo->getUser($user->getUserName());
                        header("Location: /$parentFolderName/$folderName/myaccount.php");
                        exit;
                    }
                    
                } else {
                    $_SESSION['snackbar_message'] = "The entered current password is incorrect.";
                    $_SESSION['snackbar_type'] = "error";
                    $_SESSION['snackbar_duration'] = 5000;
                    header("Location: /$parentFolderName/$folderName/myaccount.php");
                    exit;
                }
            } else {
                $_SESSION['snackbar_message'] = "Failed to update password. Your new password is not strong enough.";
                $_SESSION['snackbar_type'] = "error";
                $_SESSION['snackbar_duration'] = 5000;
                header("Location: /$parentFolderName/$folderName/myaccount.php");
                exit;
            }
        } else {
            $_SESSION['snackbar_message'] = "The entered passwords do not match. Please ensure both entries are identical.";
            $_SESSION['snackbar_type'] = "warning";
            $_SESSION['snackbar_duration'] = 8000;
            header("Location: /$parentFolderName/$folderName/myaccount.php");
            exit;
        }
    } else if (empty($newPassword) && empty($repeatNewPassword) && !empty($currentPassword)) {
        $_SESSION['snackbar_message'] = "Please enter new passwords.";
        $_SESSION['snackbar_type'] = "warning";
        $_SESSION['snackbar_duration'] = 8000;
        header("Location: /$parentFolderName/$folderName/myaccount.php");
        exit;
    }  
    elseif(empty($newPassword) && empty($repeatNewPassword) && empty($currentPassword)) {

        // Handle update of other user information
        $mail = $_POST['email'];
        $username = $_POST['username'];
        $is_validUserName = isValidEmail($mail);
        $is_validemail = isValidEmail($username);
        print_r($username);

        if (
            !empty($_POST["firstName"]) &&
            !empty($_POST["lastName"]) &&
            !empty($_POST["address"]) &&
            !empty($_POST["city"]) &&
            !empty($_POST["country"]) &&
            !empty($_POST["email"])
        ) {
            if ($is_validUserName && $is_validemail) {
                $country = getCountryNameByCode($_POST['country']) ; 
                $data = array(
                    'CustomerID' => $user->getCustomerID(),
                    'FirstName' => $_POST['firstName'],
                    'LastName' => $_POST['lastName'],
                    'Address' => $_POST['address'],
                    'City' => $_POST['city'],
                    'Region' => $_POST['region'],
                    'Country' => $country,
                    'Postal' => $_POST['postal'],
                    'Phone' => $_POST['phone'],
                    'Email' => $_POST['email'],
                );

                $data1 = [
                    'CustomerID' => $user->getCustomerID(),
                    'UserName' => $_POST['username'],
                    'DateLastModified' => date('Y-m-d H:i:s')
                ];
          
                // Create customer and customer login objects and update repository
                $customer = new Customer($data);
                $Customerlogon = new Customerlogin($data1);
                $repo = new UserRepository(new Database());

                if ($repo->updateCustomerlogon($Customerlogon) && $repo->updateCustomer($customer)) {
             
                    $_SESSION['snackbar_message'] = "All data updated successfully.";
                    $_SESSION['snackbar_type'] = "success";
                    $_SESSION['snackbar_duration'] = 5000;

                    // Refresh user and user info in session
                    $_SESSION["user"] = $repo->getUser($Customerlogon->getUserName());
                    $_SESSION["userInfo"] = $repo->getUserbyId($customer->getCustomerID());

                    header("Location: /$parentFolderName/$folderName/myaccount.php");
                    exit;
                }
                else{
                    $_SESSION['snackbar_message'] = "Failed. This Username address is already in use. You cannot change your Username to this address.";
                    $_SESSION['snackbar_type'] = "error";
                    $_SESSION['snackbar_duration'] = 5000;
                    header("Location: /$parentFolderName/$folderName/myaccount.php");
                    exit;
                }
            } else {
                $_SESSION['snackbar_message'] = "Failed to update data. Please ensure both email and username are valid and should be valid email addresses.";
                $_SESSION['snackbar_type'] = "warning";
                $_SESSION['snackbar_duration'] = 8000;
                header("Location: /$parentFolderName/$folderName/myaccount.php");
                exit;
            }
        } else {
            $_SESSION['snackbar_message'] = "Error: One or more required fields are empty. Please ensure all required fields are filled in.";
            $_SESSION['snackbar_type'] = "warning";
            $_SESSION['snackbar_duration'] = 10000;
            header("Location: /$parentFolderName/$folderName/myaccount.php");
            exit;
        }
    }
    else{
        $_SESSION['snackbar_message'] = "Please enter both new passwords.";
        $_SESSION['snackbar_type'] = "warning";
        $_SESSION['snackbar_duration'] = 8000;
        header("Location: /$parentFolderName/$folderName/myaccount.php");
        exit;
    }
}

?>


    <?php include '../includes/header.php'; ?>

<?php if ($isAdmin || $isUser): ?>
      <div class="main">
      <div class="myaccount">
        <div id="content" class="p-4 p-md-5">
            <div class="container light-style flex-grow-1 container-p-y">
                <h4 class="font-weight-bold py-3 mb-4 d-flex align-items-center"></h4>

                <form action="" method="POST">
                    <div class="card overflow-hidden">
                        <div class="row no-gutters row-bordered row-border-light">
                            <div class="col-md-3 pt-0">
                                <div class="list-group list-group-flush account-settings-links">
                                    <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general"><i class="bi bi-person-fill mr-2"></i>General</a>
                                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-address"><i class="bi bi-geo-alt-fill mr-2"></i>Address</a>
                                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-info"><i class="bi bi-info-circle-fill mr-2"></i>Info</a>
                                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-change-password"><i class="bi bi-lock-fill mr-2"></i>Change password</a>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="account-general">
                                        <hr class="border-light m-0">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control mb-1" name="username" value="<?php echo htmlspecialchars($user->getUserName()); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($userInfo->getLastName()); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($userInfo->getFirstName()); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">E-mail</label>
                                                <input type="text" class="form-control mb-1" name="email" value="<?php echo htmlspecialchars($userInfo->getEmail()); ?>">
                                                <div class="alert alert-warning mt-3">
                                                Please ensure your email and your username is a valid email adress.<br>
                                              
                                                </div>
                                            </div>
                                     
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-change-password">
                                        <div class="card-body pb-2">
                                            <div class="form-group">
                                                <label class="form-label">Current password</label>
                                                <input type="password" class="form-control" name="currentPassword">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">New password</label>
                                                <input type="password" class="form-control" name="newPassword">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Repeat new password</label>
                                                <input type="password" class="form-control" name="repeatNewPassword">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-address">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="inputAddress" class="form-label"><i class="bi bi-house-door-fill"></i> Address</label>
                                                <input type="text" class="form-control" id="inputAddress" name="address" value="<?php echo htmlspecialchars($userInfo->getAddress()); ?>">
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group ">
                                                    <label for="inputCity" class="form-label"><i class="bi bi-geo-alt-fill"></i> City</label>
                                                    <input type="text" class="form-control" id="inputCity" name="city" value="<?php echo htmlspecialchars($userInfo->getCity()); ?>">
                                                </div>
                                                <div class="form-group ">
                                                    <label for="inputRegion" class="form-label"><i class="bi bi-geo-alt"></i> Region</label>
                                                    <input type="text" class="form-control" id="inputRegion" name="region" value="<?php echo htmlspecialchars($userInfo->getRegion()); ?>">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                            <div class="form-group">
                                            <label for="inputCountry" class="form-label"><i class="bi bi-flag"></i> Country</label>
                                            <select type="text" class="form-control" id="inputCountry" name="country">
                                            <option value="">Choose...</option>
                                            <?php

                                         
                                            $countries = json_decode($countries, true);
                                            $countryKeys = array_keys($countries);

                                            $countryName = $userInfo->getCountry();
                                            $countryIndex = array_search($countryName, array_column($countries, 'name'));
                                            $countryCode = $countryKeys[$countryIndex];
                                            if ($countryCode != null) {
                                            foreach ($countries as $code => $name) {
                                                echo "<option value=\"$code\"";
                                                // Überprüfen, ob der Ländercode mit dem gefundenen Ländercode übereinstimmt
                                                if ($code === $countryCode) {
                                                echo ' selected';
                                                }
                                                echo ">" . htmlspecialchars($name['name']) . "</option>";

                                            }

                                            } else {
                                            // Wenn $userInfo->getCountry() kein Array ist, eine Standardoption anzeigen
                                            echo "<option value=\"\">Invalid Country Data</option>";
                                            }

                                            ?>

                                        </select>
                                        </div>
                                                    <div class="form-group">
                                                    <label for="inputPostal" class="form-label"><i class="bi bi-envelope-fill"></i> Postal</label>
                                                    <input type="text" class="form-control" id="inputPostal" name="postal" value="<?php echo htmlspecialchars($userInfo->getPostal()); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-info">
                                        <div class="card-body pb-2">
                                            <div class="form-group">
                                                <label class="form-label">Date Joined</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user->getDateJoined()); ?>" readonly>
                                            </div>
                                        </div>
                                        <hr class="border-light m-0">
                                        <div class="card-body pb-2">
                                            <h6 class="mb-4">Contacts</h6>
                                            <div class="form-group">
                                                <label for="inputPhone" class="form-label"><i class="bi bi-telephone-fill"></i> Phone</label>
                                                <input type="text" class="form-control" id="inputPhone" name="phone" value="<?php echo htmlspecialchars($userInfo->getPhone()); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Date Last Modified</label>
                                                <input type="text"  class="form-control" value="<?php echo htmlspecialchars($user->getDateLastModified()); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" name="save_changes">Save changes</button>&nbsp;
                        <button type="button" class="btn btn-default">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'snackbar.php'; ?>
    </div>
 
    <?php include '../includes/footer.php'; ?>
  <?php endif; ?>
