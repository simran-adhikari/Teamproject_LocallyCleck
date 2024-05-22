<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="filecss/cart.css" />
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
    <nav class="navbar">
        <div class="navdiv">
            <div class="logo">
                <a href="Home.php"><img src="resource/logo.png" alt="logo"/></a>
            </div>
            <div class="extra">
                <p><a href="Home.php">Locally<span>Cleck</span></a></p>
            </div>
            <div class="box">
                <input type="text" name=""><i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Categories</button>
                <div class="dropdown-content">
                    <a href="#">Baker</a>
                    <a href="#">Green Grocer</a>
                    <a href="#">FishMonger</a>
                    <a href="#">Butcher</a>
                    <a href="#">Delicatessen</a>
                </div>
            </div>
            <ul>
                <?php if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <!-- User is logged in -->
                    <li><a href="#"><i class="fa-solid fa-user"></i>Profile</a></li>
                    <li><a href="#"><i class="fa-solid fa-heart"></i>Wishlist</a></li>
                    <li><a href="#"><i class="fa-solid fa-cart-shopping"></i>Cart</a></li>
                    <li><a href="logout.php"><i class="fa-solid fa-sign-out"></i>Logout</a></li>
                <?php else: ?>
                    <!-- User is not logged in -->
       <!--             <li><a href="customersignin.html"><i class="fa-solid fa-user"></i>Customer Signin</a></li> -->
         <!--              <li><a href="Tradersignin.html"><i class="fa-solid fa-home"></i>Trader Signin</a></li>  -->
         <!--              <li><a href="#"><i class="fa-solid fa-cart-shopping"></i></a></li> -->
                <?php endif; ?>
            </ul>
        </div>
    </nav>

<div class="main-content">
 <?php

include "connect.php"; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle as needed
    header("Location: customersignin.html");
    exit;
}

// Get current user's cart ID (which is the user ID)
$userId = $_SESSION['user_id'];

// SQL query to fetch cart items and corresponding product details
$sql = "SELECT p.Product_ID, p.Product_Name, p.Product_Price, p.Image
        FROM product p
        JOIN cart_product cp ON p.Product_ID = cp.Product_ID
        WHERE cp.Cart_ID = :userId";

// Prepare the SQL statement
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ':userId', $userId);

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
    echo '<img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $productName . '" class="product-image">';
    echo '<div class="product-details">';
    echo '<h3>' . $productName . '</h3>';
    echo '<p>Price: $' . $productPrice . '</p>';


    echo '<div class="quantity-container">';
    echo '<label for="quantity_' . $productId . '">Quantity:</label>';
    echo '<input type="number" id="quantity_' . $productId . '" name="quantity_' . $productId . '" value="1" min="1">';
    echo '</div>'; // Close quantity-container



    echo '</div>'; // Close product-details
    echo '<div class="delete-container">';
    echo '<button class="delete-btn">Delete</button>';
    echo '</div>'; // Close delete-container
    echo '</div>'; // Close cart-item

}

?>
<div class="order-section">
            <div class="order-box">
                <h3>Order Details</h3>
                <label for="collection-day">Collection Day:</label>
                <select id="collection-day" name="collection-day">
                    <option value="1">Wednesday</option>
                    <option value="2">Thursday</option>
                    <option value="3">Friday</option>
                </select>
                <br>
                <br>
                <label for="promo-code">Add Promo Code:</label>
                <input type="text" id="promo-code" name="promo-code">
                <br>
                <button class="place-order-btn">Place Order</button>
            </div>
        </div>
    </div>

</div>



<footer class="section-p1">
      <div class="col">
          <img class="logo" src="logo.png" alt="image" height="60px" width="60px">
          <h4>Contact</h4>
          <p><strong>Address:</strong> 4600 Nepal, Kathmandu, Thapathali, TT TBC</p>
          <p><strong>Phone:</strong> +977 9812455645</p>
          <p><strong>Hours:</strong> 10:00 - 18:00, Sun - Fri</p><br>
          <div class="follow">
              <h4>Follow us</h4>
              <div class="icon" >
                  <i class="fa-brands fa-facebook"></i>
                  <i class="fa-brands fa-twitter"></i>
                  <i class="fa-brands fa-instagram"></i>
                  <i class="fa-brands fa-pinterest-p"></i>
                  <i class="fa-brands fa-youtube"></i>
              </div>
          </div>
      </div>

      <div class="col">
          <h4>About</h4>
          <a href="aboutus.html">About us</a>
          <a href="#">Delivery Information</a>
          <a href="#">Privay Policy</a>
          <a href="#">Terms & Conditions</a>
          <a href="#">Contact Us</a>
      </div>

      <div class="col">
          <h4>My Account</h4>
          <a href="#">Sign In</a>
          <a href="#">View Cart</a>
          <a href="#">My Whislist</a>
          <a href="#">Track My Order</a>
          <a href="#">Help</a>
      </div>

      <div class="col install">
          <h4>Install App</h4>
          <p>From App Store or Google Play</p>
          <div class="row">
              <img src="syau.jpg" alt="image">
              <img src="play.jpg" alt="image">
          </div>
              <p>Secure Payment Gateways</p>
              <img src="pay2.png" alt="image">
      </div>

      <div class="copyright">
          <p>&copy; 2024, DEspans - EOS IT Project Making my own Website </p>
      </div>
  </footer>

  </body>
</html>