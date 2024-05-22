<?php
session_start();
if (isset($_SESSION['display_name'])) {
    $traderName = $_SESSION['display_name'];
} else {
    $traderName='Trader';
        // Check if user is logged in
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
    <link rel="stylesheet" href="addProduct.css">
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

<?php
include("../connect.php");

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details from the database
    $query = "SELECT * FROM Product WHERE product_id = :product_id";
    $stmt = oci_parse($connection, $query);
    oci_bind_by_name($stmt, ":product_id", $product_id);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $productName = $row['PRODUCT_NAME'];
        $price = $row['PRODUCT_PRICE'];
        $description = $row['DESCRIPTION'];
        $quantity = $row['QUANTITY'];
        $allergyInfo = $row['ALLERGY_INFORMATION'];
        $category = $row['CATEGORY_ID'];
        $maxOrder = $row['MAX_ORDER'];
        $minOrder = $row['MIN_ORDER'];
        $offerid=$row['OFFER_ID'];
        // Assuming $offerType is set based on some logic or default value
       // $offerType = 1; // Defaulting to 5% off for example
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "Product ID not provided.";
    exit();
}


?>

<div class="content">
    <div class="add-product-container">
        <h2>Update Product</h2>
        <form action="php/productupdatehandle.php?product_id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="form-group">
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" value="<?php echo $productName; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $price; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo $quantity; ?>" required>
            </div>
            <div class="form-group">
                <label for="allergyInfo">Allergy Information:</label>
                <input type="text" id="allergyInfo" name="allergyInfo" value="<?php echo $allergyInfo; ?>">
            </div>
            <div class="form-group">
                <label for="category">Choose Category:</label>
                <select id="category" name="category" required>
                    <option value="1" <?php if ($category == 1) echo 'selected'; ?>>Meat</option>
                    <option value="2" <?php if ($category == 2) echo 'selected'; ?>>Fruit & Veggies</option>
                    <option value="3" <?php if ($category == 3) echo 'selected'; ?>>Sea-Food</option>
                    <option value="4" <?php if ($category == 4) echo 'selected'; ?>>Bakery Items</option>
                    <option value="5" <?php if ($category == 5) echo 'selected'; ?>>Deli Goods</option>
                </select>
            </div>
            <div class="form-group">
                <label for="maxOrder">Max Order:</label>
                <input type="number" id="maxOrder" name="maxOrder" value="<?php echo $maxOrder; ?>" required>
            </div>
            <div class="form-group">
                <label for="minOrder">Min Order:</label>
                <input type="number" id="minOrder" name="minOrder" value="<?php echo $minOrder; ?>" required>
            </div>
            <div class="form-group">
                <label for="offerType">Offer Type:</label>
                    <select id="offerType" name="offerType" required>
                    <option value="1" <?php if ($offerid == 1) echo 'selected'; ?>>No Offer</option>
                    <option value="2" <?php if ($offerid == 2) echo 'selected'; ?>>5% off</option>
                    <option value="3" <?php if ($offerid == 3) echo 'selected'; ?>>10% off</option>
                    <option value="4" <?php if ($offerid == 4) echo 'selected'; ?>>15% off</option>
                    <option value="5" <?php if ($offerid == 5) echo 'selected'; ?>>20% off</option>

                    </select>
            </div>
            <div class="form-group">
                <label for="productImage">Product Image:</label>
                <input type="file" id="productImage" name="productImage">
            </div>
            <button type="submit" class="add-product-btn">Update Product</button>
        </form>
    </div>
</div>

<?php oci_close($connection); ?>

</body>
</html>
