<?php
session_start();
if (isset($_SESSION['display_name'])) {
    $traderName = $_SESSION['display_name'];
} else {
    $traderName='Trader';
    if (!isset($_SESSION['trader_id'])) {
        // Redirect to login page if user is not logged in
        header("Location: ../Home.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/traderdashboardcss.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Add Product</title>
</head>
<body class="montserrat-text">

<?php

include("nav/header.php");
?>

    <!-- Dashboard Menu -->

<?php
include("nav/sidenav.php");
?>


    <div class="content">
        <div class="add-product-container">
            <h2>Add Product</h2>
            <form action="php/insert_product.php" method="POST" enctype="multipart/form-data">
                <!-- Existing form elements -->
                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName" required>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input id="profile-image-upload" type="file" name="profile_image"  required>
                </div>
                <div class="form-group">
                    <label for="allergyInfo">Allergy Information:</label>
                    <input type="text" id="allergyInfo" name="allergyInfo">
                </div>
                <div class="form-group">
                    <label for="category">Choose Category:</label>
                    <select id="category" name="category" required>
                        <option value="1">Meat</option>
                        <option value="2">Fruits & Veggies</option>
                        <option value="3">Sea-Food</option>
                        <option value="4">Bakery Items</option>
                        <option value="5">Deli Goods</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="maxOrder">Max Order:</label>
                    <input type="number" id="maxOrder" name="maxOrder" required>
                </div>
                <div class="form-group">
                    <label for="minOrder">Min Order:</label>
                    <input type="number" id="minOrder" name="minOrder" required>
                </div>
            
                <!-- New form group for OfferType -->
                <div class="form-group">
                    <label for="offerType">Offer Type:</label>
                    <select id="offerType" name="offerType" required>
                        <option value="1">No Offer</option>
                        <option value="2">5% off</option>
                        <option value="3">10% off</option>
                        <option value="4">15% off</option>
                        <option value="5">20% off</option>

                    </select>
                </div>
            
                <button type="submit" class="add-product-btn">Add Product</button>
            </form>
        </div>
    </div>

</body>
</html>
