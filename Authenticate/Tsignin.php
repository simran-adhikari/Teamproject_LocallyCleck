<?php
session_start(); // Start session to store user ID

// Establish Oracle database connection
include "../connect.php";


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and password from the login form
    $email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Validate input (ensure email and password are not empty)
    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit(); // Terminate script execution if input is empty
    }

    // Prepare SQL statement to check if email and password match in Trader table
// Prepare SQL statement to check if email, password, and ISVERIFIED='Y' match in Trader table
$sql = "SELECT * FROM Trader WHERE EMAIL = :email AND PASSWORD = :password AND ISVERIFIED = 'Y' AND OTPVERIFIED='Y'";
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ":email", $email);
oci_bind_by_name($stmt, ":password", $password);
oci_execute($stmt);

// Check if a matching record was found
if ($row = oci_fetch_assoc($stmt)) {
    // Authentication successful - Store user ID in session
    $_SESSION['trader_id'] = $row['TRADER_ID'];
    header("Location:../TraderDashboard/TraderDashboard.php"); // Redirect to homepage
    exit(); // Terminate script execution after successful authentication
} else {
    // Authentication failed - Display error message
    header("Location: ERROR.html");
    exit(); // Terminate script execution upon authentication failure
}

    // Free statement resources
    oci_free_statement($stmt);
}

// Close the database connection
oci_close($connection);
?>
