<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Redirect or handle unauthorized access
    header("Location: ../customersignisn.html"); // Redirect to login page
    exit();
}

// Include database connection
include "../connect.php";

// Get product details from POST request
if (isset($_POST['productName'])) {
    $productName = $_POST['productName'];

    // Fetch customer ID based on session (user ID)
    $customerId = $_SESSION['user_id']; // Assuming you store customer ID in session

    // Prepare SQL to get product ID based on product name
    $productSql = "SELECT Product_ID FROM Product WHERE Product_Name = :productName";

    // Prepare the SQL statement
    $productStmt = oci_parse($connection, $productSql);

    // Bind parameters
    oci_bind_by_name($productStmt, ':productName', $productName);

    // Execute the SQL statement
    oci_execute($productStmt);

    // Fetch product ID
    if ($productRow = oci_fetch_assoc($productStmt)) {
        $productId = $productRow['PRODUCT_ID'];

        // Insert into cart_product table
        $insertSql = "INSERT INTO cart_product (cart_id, product_id) VALUES (:customerId, :productId)";

        // Prepare the SQL statement
        $insertStmt = oci_parse($connection, $insertSql);

        // Bind parameters
        oci_bind_by_name($insertStmt, ':customerId', $customerId);
        oci_bind_by_name($insertStmt, ':productId', $productId);

        // Execute the SQL statement
        if (oci_execute($insertStmt)) {
            echo "Product added to cart successfully!";
        } else {
            echo "Failed to add product to cart.";
        }
    } else {
        echo "Product not found.";
    }
}else {
    echo "Invalid request.";
}

// Close database connection
oci_close($connection);
?>