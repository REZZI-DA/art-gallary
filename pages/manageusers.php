
<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../repositories/userRepository.php');

include __DIR__ . '/../includes/functions.php';

session_start();
$countriesJson = file_get_contents('https://cdn.jsdelivr.net/npm/countries-list/dist/countries.min.json');
$countries = json_decode($countriesJson, true);
// Check if user is logged in as an admin; otherwise, redirect to error page
$isAdmin = isset($_SESSION['adminlogin']) && $_SESSION['adminlogin'] === true;
$isUser = isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true;
if(!$isUser && !$isAdmin){
     
    $_SESSION['error_message'] =  "You are not logged in. Please log in.";
    $_SESSION['page'] = 'login.php';
    $_SESSION['back_to']='Go to Login';
     header("Location: error.php");
     exit;
}
if (!$isAdmin) {
    $_SESSION['error_message'] = "You are not logged in as an administrator and therefore do not have permission to view this page.";
    $_SESSION['page'] = 'home.php'; 
    $_SESSION['back_to'] = 'Back to Home'; 

    header("Location: error.php");
    exit;

}
if (isset($_SESSION['user'])) {
    $currentuser = $_SESSION["user"];

}
$fileName = basename(__FILE__);
$folderName = basename(dirname(__FILE__));
$parentFolderName = basename(dirname(dirname(__FILE__)));
$registerUrl = "/$parentFolderName/$folderName/index.php";

// Handle deactivating customer based on GET request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["customer_id_Deactive"]) && $_GET["form_type"] == "Deactive") {
    $customerId = $_GET['customer_id_Deactive'];

    if($currentuser->getCustomerID() != $_GET["customer_id_Deactive"]){
        $repo = new userRepository(new Database());
        $repo->deactiveCustomer($customerId);
    }
    else{

        $_SESSION['snackbar_message'] = "Error: You cannot deactivate your own account.";
        $_SESSION['snackbar_type'] = "error";
        $_SESSION['snackbar_duration'] = 8000;
     
        header("Location: /$parentFolderName/$folderName/manageusers.php");
        exit;
    }

// Handle updating customer based on POST request
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["customer_id_update"]) && $_POST["form_type"] == "Update") {
    if (!empty($_POST["customer_id_update"]) && 
    !empty($_POST["first_name"]) && 
    !empty($_POST["last_name"]) && 
    !empty($_POST["address"]) && 
    !empty($_POST["city"]) && 
    !empty($_POST["country"]) && 
    !empty($_POST["email"])){

    
    $data = array(
        'CustomerID' => $_POST["customer_id_update"],
        'FirstName' => $_POST["first_name"],
        'LastName' => $_POST["last_name"],
        'Address' => $_POST["address"],
        'City' => $_POST["city"],
        'Region' => $_POST["region"],
        'Country' => $_POST["country"],
        'Postal' => $_POST["postal"],
        'Email' => $_POST["email"],
        'Phone' => $_POST["phone"]
    );

    // Determine user role and status for updating
    $typ = $_POST["role"] == 'admin' ? 1 : 0;
    $state = $_POST["status"] == 'active' ? 1 : 0;
   echo $_POST["customer_id_update"];
    $data1 = [
        'CustomerID' => $_POST["customer_id_update"],
        'Type' => $typ,
        'State' => $state,
         'UserName' =>$_POST['username'],
        'DateLastModified' => date('Y-m-d H:i:s')
    ];

    $mail = $_POST['email'];
  
    $is_validUserName = isValidEmail($mail);
   
    if($is_validUserName && $is_validUserName){
    $customer = new Customer($data);
    $customerlogon = new Customerlogin($data1);
    $repo = new userRepository(new Database());

 
    if ($repo->updateCustomerlogon($customerlogon) && $repo->updateCustomer($customer)) {
             
        $_SESSION['snackbar_message'] = "All data updated successfully.";
        $_SESSION['snackbar_type'] = "success";
        $_SESSION['snackbar_duration'] = 5000;

        // Refresh user and user info in session
        $_SESSION["user"] = $repo->getUser($currentuser->getUserName());
        $_SESSION["userInfo"] = $repo->getUserbyId($currentuser->getCustomerID());

        header("Location: /$parentFolderName/$folderName/manageusers.php");
        exit;
    }
    }
        else {
            $_SESSION['snackbar_message'] = "Failed to update data. Please ensure the email address is valid.";
            $_SESSION['snackbar_type'] = "error";
            $_SESSION['snackbar_duration'] = 8000;
            header("Location: /$parentFolderName/$folderName/manageusers.php");
            exit;
        }
    }else
    {
        $_SESSION['snackbar_message'] = "Error: One or more required fields are empty. Please ensure all required fields are filled in.";
        $_SESSION['snackbar_type'] = "warning";
        $_SESSION['snackbar_duration'] = 10000;
        header("Location: /$parentFolderName/$folderName/manageusers.php");
        exit;
    }
} 

// Retrieve all users and paginate the results
$repo = new userRepository(new Database());
$users = $repo->getAllusers();

// Number of users per page
$usersPerPage = 7;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page); // Page cannot be less than 1

$startIndex = ($page - 1) * $usersPerPage;

$currentUsers = array_slice($users, $startIndex, $usersPerPage);

$totalPages = ceil(count($users) / $usersPerPage);
?>

	<?php include '../includes/header.php'; ?>

<div class="manageusers">
        <!-- Page Content  -->
    <div id="content" class="p-4 p-md-5">
      
            <?php if ($isAdmin): ?>

                <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Manage <b>Users</b></h2>
                        </div>
                        <!-- <div class="col-sm-6">
                            <a href="#addEmployeeModal" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal"><i class="material-icons">&#xE147;</i> <span>Add New User</span></a>
                            <a href="#deaktivEmployeeModal" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deaktivEmployeeModal" ><i class="material-icons">&#xE15C;</i> <span>Deactive</span></a>
                        </div> -->
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <span class="custom-checkbox">
                                    <input type="checkbox" id="selectAll">
                                    <label for="selectAll"></label>
                                </span>
                            </th>
                            <th>CustomerID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Region</th>
                            <th>Country</th>
                            <th>Postal</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Roll</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currentUsers as $user): ?>
                            <tr>
                                <td>
                                    <span class="custom-checkbox">
                                        <input type="checkbox" id="checkbox<?php echo $user->getCustomerId(); ?>" name="options[]" value="1">
                                        <label for="checkbox<?php echo $user->getCustomerId(); ?>"></label>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user->getCustomerId()); ?></td>
                                <td><?php echo htmlspecialchars($user->getFirstName()); ?></td>
                                <td><?php echo htmlspecialchars($user->getLastName()); ?></td>
                                <td><?php echo htmlspecialchars($user->getAddress()); ?></td>
                                <td><?php echo htmlspecialchars($user->getCity()); ?></td>
                                <td><?php echo htmlspecialchars($user->getRegion()); ?></td>
                                <td>
                                    
                                
                                <?php 
                                
                                            $countryKeys = array_keys($countries);

                                            $countryIndex = array_search($user->getCountry(), array_column($countries, 'name'));
                                            $countryCode = $countryKeys[$countryIndex];
                                            if ($countryCode != null) {
                                            foreach ($countries as $code => $name) {
                           
                                                if ($code === $countryCode) {
                                                echo htmlspecialchars($name['name']);
                                                }
                                               

                                            }

                                            } 
                                
                                ?>
                            
                            </td>
                                <td><?php echo htmlspecialchars($user->getPostal()); ?></td>
                                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                <td><?php echo htmlspecialchars($user->getPhone()); ?></td>
                                <td>
                                    <?php 
                                    $status = $user->getStatus();
                                    echo htmlspecialchars($status == 0 || $status == 1 ? 'Active' : $status); 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $typ = $user->getTyp();
                                
                                    if ($typ == 0) {
                                        echo 'User';  // Oder 'Benutzer'
                                    } elseif ($typ == 1) {
                                        echo 'Admin';
                                    } else {
                                        echo htmlspecialchars($typ);
                                    }
                                    ?>
                                </td>
                             
                                <td>
                                <a href="#editEmployeeModal" class="edit" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"  onclick="setCustomer('<?php echo htmlspecialchars(json_encode(unserialize($user->serialize()))); ?>');"> <i class="material-icons" data-bs-toggle="tooltip" title="Edit">&#xE254;</i></a>
 
                                <a href="#DeactiveEmployeeModal" class="Deactive" data-bs-toggle="modal" data-bs-target="#DeactiveEmployeeModal" onclick="setCustomerId(<?php echo htmlspecialchars($user->getCustomerID()); ?>)">
                        <?php if ($user->getCustomerId() == $currentuser->getCustomerId()): ?>
                            <i class="material-icons" style="color: red;" data-bs-toggle="tooltip" title="Deactivate">&#xE15C;</i>
                        <?php else: ?>
                            <i class="material-icons" data-bs-toggle="tooltip" title="Deactivate">&#xE15C;</i>
                        <?php endif; ?>
                    </a>
    


                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            
                <div class="clearfix">
                    <div class="hint-text">Showing <b><?php echo count($currentUsers); ?></b> out of <b><?php echo count($users); ?></b> users</div>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a href="?page=<?php echo $page - 1; ?>" class="page-link">Previous</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><a href="#" class="page-link">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page)
                                echo 'active'; ?>"><a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item"><a href="?page=<?php echo $page + 1; ?>" class="page-link">Next</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><a href="#" class="page-link">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal HTML -->
    <div id="editEmployeeModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manageusers.php" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">Edit User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label hidden>Username</label>
                        <input type="text" class="form-control" name="username" id="username" hidden>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" id="city" required>
                    </div>
                    <div class="form-group">
                        <label>Region</label>
                        <input type="text" class="form-control" name="region" id="region" >
                    </div>
                  
                    <div class="form-group">
                    <label for="inputCountry" class="form-label"><i class="bi bi-flag"></i> Country</label>
        <select type="text"  name="country" class="form-control" id="countrySelect">
      
   
        <?php foreach ($countries as $country): ?>
            <?php $countryName = $country['name']; ?>
            <option value="<?= htmlspecialchars($countryName) ?>">
                <?= htmlspecialchars($countryName) ?>
            </option>
        <?php endforeach; ?>
    </select>
    </div>
                    <div class="form-group">
                        <label>Postal</label>
                        <input type="text" class="form-control" name="postal" id="postal" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" required>
                    </div>
                    <div class="form-group">
    <label for="status">Status</label>
    <select class="form-control" name="status" id="status" required>
        <option value="active">Active</option>
        <option value="deactive">Deactive</option>
    </select>
</div>

<div class="form-group">
    <label for="role">Role</label>
    <select class="form-control" name="role" id="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
</div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id_update" id="customer_id_update" value="">
                    <input type="button" class="btn btn-default" data-bs-dismiss="modal" value="Cancel">
                    <input type="submit" name="form_type" class="btn btn-info" value="Update">
                </div>
            </form>
        </div>
    </div>
    </div>


    <!-- Deactive Modal HTML -->
    <div id="DeactiveEmployeeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="GET" >
                    <div class="modal-header">
                        <h4 class="modal-title">Deactive User</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to Deactive these Records?</p>
                        <p class="text-warning"><small>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                    <input type="hidden" name="customer_id_Deactive" id="customer_id_Deactive" value="">
                        <input type="button" class="btn btn-default" data-bs-dismiss="modal" value="Cancel">
                        <input type="submit" id="DeactiveUserBtn" name="form_type" class="btn btn-danger" value="Deactive">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
 </div>


 <?php include 'snackbar.php'; ?>
 <?php include '../includes/footer.php'; ?>
<script>
$(document).ready(function(){
    // Activate tooltip
    $('[data-toggle="tooltip"]').tooltip();
    
    // Select/Deselect checkboxes
    var checkbox = $('table tbody input[type="checkbox"]');
    $("#selectAll").click(function(){
        if(this.checked){
            checkbox.each(function(){
                this.checked = true;                        
            });
        } else{
            checkbox.each(function(){
                this.checked = false;                        
            });
        } 
    });
    checkbox.click(function(){
        if(!this.checked){
            $("#selectAll").prop("checked", false);
        }
    });
});
function setCustomerId(customerId) {
        document.getElementById('customer_id_Deactive').value = customerId;
    }
    function setCustomer(user) {
     
        var userData = JSON.parse(user);
      
    document.getElementById('first_name').value = userData.firstName;
    document.getElementById('last_name').value = userData.lastName;
    document.getElementById('address').value = userData.address;
    document.getElementById('city').value = userData.city;
    document.getElementById('region').value = userData.region;
    // document.getElementById('country').value = userData.country;



            const selectedCountryName =  userData.country;
            
            // Finde das <select>-Element und setze den selektierten Wert
            const selectElement = document.getElementById('countrySelect');
            for (let option of selectElement.options) {
                if (option.value === selectedCountryName) {
                    option.selected = true;
                    break;
                }
            }
     
    document.getElementById('postal').value = userData.postal;
    document.getElementById('email').value = userData.email;
        document.getElementById('username').value = userData.email;
    document.getElementById('phone').value = userData.phone;
    document.getElementById('status').value = userData.status == 1 ? "active" : "deactive";
  // Assuming userData.type contains the values 0 or 1
document.getElementById('role').value = userData.typ == 1 ? "admin" : "user";

    document.getElementById('customer_id_update').value = userData.customerID;
}
const phoneInput = document.getElementById('phone');

if (phoneInput) {
    phoneInput.addEventListener('input', function () {
        let phone = phoneInput.value.replace(/[^\d+]/g, '');

        if (!phone.startsWith('+')) {
            phone = '+' + phone.substring(1);
        }

        phone = phone.substring(0, 13);
        phone = phone.replace(/\D/g, '')
            .replace(/^(\+\d{1,3})?(\d{1,3})?(\d{1,3})?(\d{1,4})?/, (match, g1, g2, g3, g4) => {
                let result = g1 || '+';
                if (g2) result += g2;
                if (g3) result += '-' + g3;
                if (g4) result += '-' + g4;
                return result;
            });

        phoneInput.value = phone;

        const isValid = /^\+\d+([-\s\.]?[\d]+)*$/.test(phone) && phone.length <= 13;
        phoneInput.classList.toggle('is-valid', isValid);
        phoneInput.classList.toggle('is-invalid', !isValid);
    });

    phoneInput.addEventListener('keydown', function (event) {
        if (event.target.value.length >= 13 && event.key !== 'Backspace') {
            event.preventDefault();
        }
    });

    if (!phoneInput.value.startsWith('+')) {
        phoneInput.value = '+';
    }
}
</script>  
