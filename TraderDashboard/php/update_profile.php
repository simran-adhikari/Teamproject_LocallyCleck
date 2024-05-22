<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

</body>

<?php
session_start();
$trader_id = $_SESSION['trader_id']; // Assuming trader_id is fetched from session or elsewhere

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $changeName = isset($_POST['changeName']) ? $_POST['changeName'] : '';
    $changeShopName = isset($_POST['changeShopName']) ? $_POST['changeShopName'] : '';
    $changeDescription = isset($_POST['changeDescription']) ? $_POST['changeDescription'] : '';
    $changeShopType = isset($_POST['changeShopType']) ? $_POST['changeShopType'] : '';
    $shop_id = $trader_id; // Set the shop_id (replace with your logic to fetch the correct shop_id)

    // Database connection details
    include("../../connect.php");

    // Update trader's name in the trader table
    $query1 = "UPDATE trader SET name = :changeName WHERE trader_id = :trader_id";
    $statement1 = oci_parse($connection, $query1);
    oci_bind_by_name($statement1, ":changeName", $changeName);
    oci_bind_by_name($statement1, ":trader_id", $trader_id);
    oci_execute($statement1);

    // Update shop details in the shop table
    $query2 = "UPDATE shop 
               SET shop_name = :changeShopName, description = :changeDescription, shop_type = :changeShopType
               WHERE shop_id = :shop_id";

    $statement2 = oci_parse($connection, $query2);
    oci_bind_by_name($statement2, ":changeShopName", $changeShopName);
    oci_bind_by_name($statement2, ":changeDescription", $changeDescription);
    oci_bind_by_name($statement2, ":changeShopType", $changeShopType);
    oci_bind_by_name($statement2, ":shop_id", $shop_id);
    oci_execute($statement2);

    // Handle file uploads for bannerImage
    if (isset($_FILES['profile_image'], $_FILES['banner_image'])) {
        // Your code for handling banner image uploads
    }

    // Handle file uploads for profileImage
    if (isset($_FILES['profile_image'], $_FILES['banner_image'])) {
        // Your code for handling profile image uploads
    }

    // Close database connection
    oci_close($connection);

    // Output SweetAlert2 notification
    echo '<script type="text/javascript">';
    echo 'Swal.fire({';
    echo '  icon: "success",';
    echo '  title: "Profile Updated",';
    echo '  showConfirmButton: false,';
    echo '  timer: 1500';
    echo '}).then(() => {';
    echo '  window.location.href = "../TraderDashboard.php";';
    echo '});';
    echo '</script>';
    exit();
} else {
    // Handle invalid request (e.g., redirect to an error page)
    //header("Location: error.php");
    echo("ERROR");
    exit();
}
?>