<?php
session_start();

// Database connection
$connection = oci_connect("diwon", "hackvayo", "//localhost/xe");
if (!$connection) {
    $error_message = oci_error();
    echo "Failed to connect to Oracle: " . $error_message['message'];
    exit();
}

// Variable to hold the update result
$update_success = false;

// Check if the user is logged in and get their user_id
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch customer details from the database
    $query = "SELECT * FROM CUSTOMER WHERE CUSTOMER_ID = :user_id";
    $statement = oci_parse($connection, $query);
    oci_bind_by_name($statement, ":user_id", $user_id);
    oci_execute($statement);

    // Fetch the row
    $row = oci_fetch_assoc($statement);

    // Assign fetched values to variables
    $first_name = $row['FIRST_NAME'];
    $last_name = $row['LAST_NAME'];
    $email = $row['EMAIL'];
    $gender = $row['GENDER'];
    $password = $row['PASSWORD'];
    // Assuming other fields exist in your table

    // Check if the form is submitted for updating the profile
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password']; // Assuming password is not hashed for simplicity, but it should be hashed in production
        $gender = $_POST['gender'];
        // Assuming other fields exist in your form

        // Update customer profile
        $update_query = "UPDATE CUSTOMER SET FIRST_NAME = :first_name, LAST_NAME = :last_name, EMAIL = :email, PASSWORD = :password, GENDER = :gender WHERE CUSTOMER_ID = :user_id";
        $update_statement = oci_parse($connection, $update_query);
        oci_bind_by_name($update_statement, ":first_name", $first_name);
        oci_bind_by_name($update_statement, ":last_name", $last_name);
        oci_bind_by_name($update_statement, ":email", $email);
        oci_bind_by_name($update_statement, ":password", $password);
        oci_bind_by_name($update_statement, ":gender", $gender);
        oci_bind_by_name($update_statement, ":user_id", $user_id);

        $result = oci_execute($update_statement);

        if ($result) {
            $update_success = true;
        } else {
            // Handle error
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/customerprofile.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet" />
    <style>
        .order-history-button {
            background-color: #28a745;
            color: white;
            border: 2px solid #218838;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-left: 20px;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .order-history-button:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
    <title>Profile</title>
</head>
<body>
<?php include("header.php"); ?>

<a href="orderhistory.php" class="order-history-button">ORDER HISTORY</a>

<div class="container">
    <h2>Your Profile</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $password; ?>">
            <i class="fas fa-eye-slash toggle-password" onclick="togglePasswordVisibility()"></i>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="M" <?php if ($gender == 'M') echo 'selected'; ?>>Male</option>
                <option value="F" <?php if ($gender == 'F') echo 'selected'; ?>>Female</option>
                <!-- Add more options if needed -->
            </select>
        </div>

        <!-- Add other fields as needed -->

        <div class="form-group">
            <button type="submit" class="submit-btn">Update Profile</button>
        </div>
    </form>
</div>

<!-- Footer -->
<?php include("footer.php"); ?>

<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        var toggleButton = document.querySelector(".toggle-password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleButton.classList.remove("fa-eye-slash");
            toggleButton.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            toggleButton.classList.remove("fa-eye");
            toggleButton.classList.add("fa-eye-slash");
        }
    }

    <?php if ($update_success): ?>
    Toastify({
        text: "Profile updated successfully!",
        duration: 300, // Duration in milliseconds
        close: false, // Show close button
        gravity: "top", // Position (top/bottom)
        position: "right", // Position (left/right/center)
        backgroundColor: "#28a745", // Green background color
        className: 'toastify-style',
    }).showToast();
    <?php endif; ?>
</script>
</body>
</html>