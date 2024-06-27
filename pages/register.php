<?php

if (!class_exists('Database')) {
    include __DIR__ . '/../config/database.php';
}

if (!class_exists('UserRepository')) {
    include __DIR__ . '/../repositories/userRepository.php';
}

include __DIR__ . '/../includes/functions.php';
$countriesJson = file_get_contents('https://cdn.jsdelivr.net/npm/countries-list/dist/countries.min.json');
$countries = json_decode($countriesJson, true);
$fileName = basename(__FILE__);
$folderName = basename(dirname(__FILE__));
$parentFolderName = basename(dirname(dirname(__FILE__)));
$loginUrl = "/$parentFolderName/$folderName/login.php";

session_start();
$is_valid = isset($_SESSION['is_valid']) ? $_SESSION['is_valid'] : null;
$email_valid = false;
$password = '';
$firstName = isset($_SESSION['firstName']) ? $_SESSION['firstName'] : '';
$lastName = isset($_SESSION['lastName']) ? $_SESSION['lastName'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$city = isset($_SESSION['city']) ? $_SESSION['city'] : '';
$region = isset($_SESSION['region']) ? $_SESSION['region'] : '';
$country = isset($_SESSION['country']) ? $_SESSION['country'] : '';
$postal = isset($_SESSION['postal']) ? $_SESSION['postal'] : '';
$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate and retrieve form data
    if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['country']) && isset($_POST['email']) && isset($_POST['password'])) {

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $region = isset($_POST['region']) ? $_POST['region'] : "";
        $country = $_POST['country'];
        $postal = isset($_POST['postal']) ? $_POST['postal'] : "";
        $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Save data in session variables
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['address'] = $address;
        $_SESSION['city'] = $city;
        $_SESSION['region'] = $region;
        $_SESSION['country'] = $country;
        $_SESSION['postal'] = $postal;
        $_SESSION['phone'] = $phone;
        $_SESSION['email'] = $email;

        // Validate password
        $is_valid = validatePassword($password);
        $email_valid = isValidEmail($email);
echo $is_valid ;
echo $email_valid ;
        if (!$is_valid || !$email_valid) {
          $_SESSION['snackbar_message'] = "Failed to register. Please ensure the email address is valid and the password is strong.";
          $_SESSION['snackbar_type'] = "error";
          $_SESSION['snackbar_duration'] = 10000;
          header("Location: /$parentFolderName/$folderName/register.php");
          exit;
      }
      
        // If all validations pass, register the user
        if ($is_valid && $email_valid && !empty($firstName) && !empty($lastName) && !empty($country) && !empty($city) && !empty($address)) {
   
            $country = getCountryNameByCode($country) ; 
            $repo = new userRepository(new Database());
            if ($repo->isUsernameExists($email)) {
              $_SESSION['snackbar_message'] = "The email address is already registered. You are already signed up with this email.";
              $_SESSION['snackbar_type'] = "error";
              $_SESSION['snackbar_duration'] = 6000;
              header("Location: /$parentFolderName/$folderName/register.php"); // Optional: Redirect back to login page on error
              exit;
            }
            elseif ($repo->registerUser($password, $firstName, $lastName, $address, $city, $country, $region, $postal, $phone, $email)) {
                $_SESSION['snackbar_message'] = "Registration successful! You can now login.";
                $_SESSION['snackbar_type'] = "success";
                $_SESSION['snackbar_duration'] = 6000;
                header("Location: /$parentFolderName/$folderName/register.php"); // Optional: Redirect back to login page on error
                exit;
            }
            else{
              header("Location: error.php");
              exit();
            }
        } else {
            $_SESSION['snackbar_message'] = "Please review your entries carefully. Ensure all required fields are correctly filled out, and fields marked with a red asterisk have passed validation.";
            $_SESSION['snackbar_type'] = "error";
            $_SESSION['snackbar_duration'] = 13000;
            header("Location: /$parentFolderName/$folderName/register.php"); // Optional: Redirect back to login page on error
            exit;
        }
    }
}
?>




<?php include '../includes/header.php'; ?>



<div id="content" class="p-4 p-md-5">

<section class="ftco-section_reg">

    <div class="container">
      <div class="row justify-content-center">
   
      </div>
      <div class="row justify-content-center">
        <div class="col-md-12 col-lg-10">
          <div class="wrap d-md-flex">
            <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
              <div class="text w-100">
                <h2>Welcome to registration</h2>
                <p>Already have an account?</p>
                <a href="<?php echo $loginUrl; ?>"class="btn btn-white btn-outline-white">Sign In</a>
              </div>
            </div>
            <div class="login-wrap p-4 p-lg-5">
              <div class="d-flex">
                <div class="w-100">
                  <h3 class="mb-4">Register</h3>
                </div>
              
              </div>
    <form  id="registrationForm" name = "registrationForm" action="register.php" method="post" class="signin-form">
    <div class="form-group mb-3">


    <label class="label" for="firstName"><i class="fas fa-star fa-xs text-warning"></i> First Name </label>
        <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First Name" value="<?php echo htmlspecialchars($firstName); ?>" required>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="lastName"> <i class="fas fa-star fa-xs text-warning"></i> Last Name</label>
        <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Last Name" value="<?php echo htmlspecialchars($lastName); ?>" required>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="address"><i class="fas fa-star fa-xs text-warning"></i> Address </label>
        <input type="text" class="form-control" name="address" id="address" placeholder="Address" value="<?php echo htmlspecialchars($address); ?>" required>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="city"> <i class="fas fa-star fa-xs text-warning"></i> City</label>
        <input type="text" class="form-control" name="city" id="city" placeholder="City" value="<?php echo htmlspecialchars($city); ?>" required>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="region">Region </label>
        <input type="text" class="form-control" name="region" id="region" placeholder="Region" value="<?php echo htmlspecialchars($region); ?>">
    </div>

    <div class="form-group mb-3">
    <label class="label" for="country"> <i class="fas fa-star fa-xs text-warning"></i> Country</label>
    <select type="text" class="form-control" name="country" id="country" placeholder="Country" required>
        <option value="">Choose...</option>
        <?php
        // Holen Sie sich die Länderdaten
   

        // Erstellen Sie die Options-Elemente
        foreach ($countries as $code => $country) {
            echo "<option value=\"$code\">" . htmlspecialchars($country['name']) . "</option>";
        }
        ?>
    </select>
</div>

    <div class="form-group mb-3">
        <label class="label" for="postal">Postal Code</label>
        <input type="text" class="form-control" name="postal" id="postal" placeholder="Postal Code" value="<?php echo htmlspecialchars($postal); ?>">
    </div>
    <div class="form-group mb-3">
        <label class="label" for="phone">Phone</label>
        <input type="text" class="form-control " 
           name="phone" id="phone" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>">
    <small id="phoneHelpBlock" class="form-text text-muted">
        Your phone number must follow this format: +123-456-7890. 
    </small>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="email"> <i class="fas fa-star fa-xs text-danger"></i> Email</label>
        <input type="email" id="email" name="email" placeholder="Enter email" class="form-control >
        id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <small id="emailHelpBlock" class="invalid-feedback">
        Please enter a valid email address.
    </small>
    </div>
    <div class="form-group mb-3">
        <label class="label" for="validationPassword"> <i class="fas fa-star fa-xs text-danger"></i> Password</label>
        <input type="password" class="form-control <?php echo isset($is_valid) ? ($is_valid ? 'is-valid' : 'is-invalid') : ''; ?>" id="validationPassword" minlength="8" name="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>" required>
        <div class="progress" style="height: 5px; margin-top: 0.2rem;">
            <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <small id="passwordHelpBlock" class="form-text text-muted">
            Your password must be 8-20 characters long, must contain special characters "!@#$%&*_?", numbers, lower and upper letters only.
        </small>
        <div id="feedbackin" class="valid-feedback">
            Strong Password!
        </div>
        <div id="feedbackirn" class="invalid-feedback">
            At least 8 characters, Number, special character Capital Letter and Small letters
        </div>
   
    </div>

    <div class="form-group">
        <button type="submit" name="submitButton" id="submitButton" class="form-control btn btn-primary submit px-3 ">Register</button>
    </div>
 
</form>


    </div>
            </div>
          </div>
        </div>
      </div>
    </div>
 
   
   
  </section>

  <div id="showLoader" style="display: none;">
    <div style="position: fixed; left: 0px; right: 0px; top: 0px; bottom: 0px; background-color: rgba(80, 80, 80, 0.5); z-index: 999;"></div>
    <div class="loader">
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
        <span class="loader-block"></span>
    </div>
    <div style="position: absolute; left: calc(50% - 43px); bottom: 37%; color: #999;">Please wait...</div>
    </div>

    </div>
    <script src="../scripts/register.min.js"></script>
    <?php include 'snackbar.php'; ?>
    <?php include '../includes/footer.php'; ?>
    
    



