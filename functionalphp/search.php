<?php
include "../connect.php"; // Database connection

// Check if the product parameter is set in the URL
if(isset($_GET['product'])) {
    $productName = $_GET['product'];

    // SQL query to check if the product exists in the database
    $sql = "SELECT * FROM PRODUCT WHERE PRODUCT_NAME = :product_name";

    // Prepare the SQL statement
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':product_name', $productName);

    // Execute the SQL statement
    oci_execute($stmt);

    // Check if the product exists
    if ($row = oci_fetch_assoc($stmt)) {
        // Product found
        echo "found";
    } else {
        // Product not found
        echo "not found";
    }
} else {
    // Product parameter not provided
    echo "Please provide a product name.";
}
?>