<?php

session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <link rel="stylesheet" href="css/wishlist.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
      integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap"
      rel="stylesheet"
    />


    <title>Document</title>
  </head>
  <body>

  <?php
  if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle as needed
    header("Location: Authenticate/customersignin.html");
    exit;
}
?>
  <?php
  include("header.php");
  ?>
<h1>PRODUCTS OF YOUR WISH</h1>

  <?php
include "connect.php"; // Database connection


// Check if user is logged in


// Get current user's wishlist ID (which is the user ID)
$userId = $_SESSION['user_id'];

// Check if delete button is clicked
if (isset($_POST['delete'])) {
    // Get the product ID to delete
    $productIdToDelete = $_POST['product_id'];
    
    // SQL query to delete product from the wishlist
    $deleteQuery = "DELETE FROM WISHLIST_PRODUCT WHERE WISHLIST_ID = :user_id AND PRODUCT_ID = :product_id";
    
    // Prepare the SQL statement
    $deleteStmt = oci_parse($connection, $deleteQuery);
    oci_bind_by_name($deleteStmt, ':user_id', $userId);
    oci_bind_by_name($deleteStmt, ':product_id', $productIdToDelete);
    
    // Execute the SQL statement
    oci_execute($deleteStmt);
}

// SQL query to fetch cart items and corresponding product details
$sql = "SELECT p.Product_ID, p.Product_Name, p.Product_Price, p.Image
        FROM WISHLIST_PRODUCT w 
        INNER JOIN PRODUCT p ON w.PRODUCT_ID = p.PRODUCT_ID 
        WHERE w.WISHLIST_ID = :user_id";

// Prepare the SQL statement
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ':user_id', $userId);

// Execute the SQL statement
oci_execute($stmt);

// Display cart items
while ($row = oci_fetch_assoc($stmt)) {
    $productId = $row['PRODUCT_ID'];
    $productName = $row['PRODUCT_NAME'];
    $productPrice = $row['PRODUCT_PRICE'];
    $imageData = $row['IMAGE']->load();

    // Encode the BLOB data as base64
    $encodedImageData = base64_encode($imageData);

    // Determine the image type based on the first few bytes of the image data
    $header = substr($imageData, 0, 4);
    $imageType = 'image/jpeg'; // default to JPEG
    if (strpos($header, 'FFD8') === 0) {
        $imageType = 'image/jpeg'; // JPEG
    } elseif (strpos($header, '89504E47') === 0) {
        $imageType = 'image/png'; // PNG
    }


    // Display each cart item

    echo '<div class="product-section">';
    echo '<div class="cart-item">';
    echo '<a href="productdetail.php?product=' . urlencode($productName) . '">';
    echo '<img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $productName . '" class="product-image">';
    echo '</a>'; // Close the <a> tag for the images
    echo '<div class="product-details">';
    echo '<h3>' . $productName . '</h3>';
    echo '<p>Price: $' . $productPrice . '</p>';

    echo '<form method="POST">';
    echo '<input type="hidden" name="product_id" value="' . $productId . '">';
    echo '</form>';



    echo '</div>'; // Close product-details


    echo '<div class="delete-container">';
    echo '<form method="POST">';
    echo '<input type="hidden" name="product_id" value="' . $productId . '">';
    echo '<button type="submit" name="delete" button class="delete-btn" >Delete</button>';
    echo '</form>';
    echo '</div>'; // Close delete-container



    echo '</div>'; // Close cart-item
}
?>

 
<?php
include("footer.php");
?>

  </body></html>