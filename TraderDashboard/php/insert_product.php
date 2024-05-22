<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

</body>


<?php
session_start();
// Database connection details
include("../../connect.php");

// Validate form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $shop_id = $_SESSION['trader_id']; // Sample shop_id (replace with actual logic to get shop_id)
    $product_name = $_POST['productName'];
    $product_price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category'];
    $allergyInfo = isset($_POST['allergyInfo']) ? $_POST['allergyInfo'] : 'None';
    $maxOrder = $_POST['maxOrder'];
    $minOrder = $_POST['minOrder'];
    $offer_ID = $_POST['offerType'];

    $profileImageData = file_get_contents($_FILES['profile_image']['tmp_name']);
    $profileImageName = $_FILES['profile_image']['name'];

        // Prepare SQL statement to insert product with BLOB image
    $query = "INSERT INTO Product (shop_id, product_name, product_price, description, quantity, category_id, ALLERGY_INFORMATION, MAX_ORDER, MIN_ORDER, IMAGE, Offer_ID)
                  VALUES (:shop_id, :product_name, :product_price, :description, :quantity, :category_id, :allergyInfo, :maxOrder, :minOrder, EMPTY_BLOB(), :offer_ID)
                  RETURNING IMAGE INTO :profile_image_data";

        $statement = oci_parse($connection, $query);
        oci_bind_by_name($statement, ":shop_id", $shop_id);
        oci_bind_by_name($statement, ":product_name", $product_name);
        oci_bind_by_name($statement, ":product_price", $product_price);
        oci_bind_by_name($statement, ":description", $description);
        oci_bind_by_name($statement, ":quantity", $quantity);
        oci_bind_by_name($statement, ":category_id", $category_id);
        oci_bind_by_name($statement, ":allergyInfo", $allergyInfo);
        oci_bind_by_name($statement, ":maxOrder", $maxOrder);
        oci_bind_by_name($statement, ":minOrder", $minOrder);
        oci_bind_by_name($statement, ":offer_ID", $offer_ID);

        // Create a new LOB descriptor for IMAGE
        $profileImageBlob = oci_new_descriptor($connection, OCI_D_LOB);
        oci_bind_by_name($statement, ':profile_image_data', $profileImageBlob, -1, OCI_B_BLOB);

        // Execute the SQL statement
        $result = oci_execute($statement, OCI_DEFAULT);

        if ($result) {
            // Save images to LOB fields
            if ($profileImageBlob->save($profileImageData)) {
                oci_commit($connection);

            echo '<script type="text/javascript">';
            echo 'Swal.fire({';
            echo '  icon: "success",';
            echo '  title: "Sucessfully Inserted",';
            echo '  showConfirmButton: false,';
            echo '  timer: 1000';
            echo '}).then(() => {';
            echo '  window.location.href = "../dashProduct.php";';
            echo '});';
            echo '</script>';


               // echo "Product Added";
                // Send email with OTP
            } else {
                oci_rollback($connection);
                echo "Failed to save image data.";
            }
    }
    oci_free_statement($statement);
    $profileImageBlob->free();

} 
    
    else {
        echo "Error uploading Product.";
    }

    // Close database connection
    oci_close($connection);
?>