<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <link rel="stylesheet" href="css/traderdetail.css" />
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


    <title>Trader Profile</title>
    <link rel="icon" type="image/x-icon" href="resource/logo.png" alt="Logo">

  </head>
  <body>

<?php
include("header.php");
include("functionalphp/toaster.php");
?>



    <?php
// Establish database connection
include("connect.php");

// Retrieve the shop_name parameter from the URL
if (isset($_GET['shop_name'])) {
    $shopName = urldecode($_GET['shop_name']);

    // Prepare SQL statement with a bind variable
    $query = "SELECT BANNERIMAGE, PROFILEIMAGE, SHOP_NAME ,DESCRIPTION
              FROM SHOP 
              WHERE SHOP_NAME = :shop_name";

    $stmt = oci_parse($connection, $query);

    // Bind the parameter
    oci_bind_by_name($stmt, ":shop_name", $shopName);

    // Execute the statement
    $result = oci_execute($stmt);

    if ($result) {
        // Fetch the row
        $row = oci_fetch_assoc($stmt);

        if ($row) {
            // Display trader details within a banner container
            echo "
            <div class='banner-container'>
                <img class='banner' src='data:image/jpeg;base64," . base64_encode($row['BANNERIMAGE']->load()) . "' alt='Banner Image'>
            </div>
            <div class='profile-container'>
                <img class='profile-image' src='data:image/jpeg;base64," . base64_encode($row['PROFILEIMAGE']->load()) . "' alt='Profile Image'>
                <div class='shop-details'>
                    <p class='shop-name'>" . htmlspecialchars($row['SHOP_NAME']) . "</p>
                    <p class='shop-description'>" . htmlspecialchars($row['DESCRIPTION']) . "</p>
                </div>
            </div>";
            
        } 
    } else {
        $error_message = oci_error($stmt);
        echo "Failed to execute query: " . $error_message['message'];
    }

    // Free the statement and close the connection
    oci_free_statement($stmt);
    oci_close($connection);
} else {
    echo 'Invalid trader selection.';
}
?>


<!-- Trader Products -->
<div class="best-sellers">
    <h2>OUR PRODUCTS</h2>
    <div class="product-list">
        <?php
    // Retrieve the shop_name parameter from the URL
    if (isset($_GET['shop_name'])) {
        $shopName = urldecode($_GET['shop_name']);

        // Prepare SQL statement to fetch products from the specified shop
        $productSql = "SELECT Image, Product_Name, Product_Price 
                       FROM Product 
                       WHERE Shop_ID = (SELECT SHOP_ID FROM SHOP WHERE SHOP_NAME = :shop_name)
                       and isverified='Y'
                       and 
                       quantity>2
                       ORDER BY Product_Price DESC";

        // Prepare the SQL statement
        $productStmt = oci_parse($connection, $productSql);

        // Bind the parameter
        oci_bind_by_name($productStmt, ":shop_name", $shopName);

        // Execute the SQL statement
        $productResult = oci_execute($productStmt);

        if ($productResult) {
            // Loop through each row of the result set
            while ($productRow = oci_fetch_assoc($productStmt)) {
                $productName = htmlspecialchars($productRow['PRODUCT_NAME']);
                $productPrice = htmlspecialchars($productRow['PRODUCT_PRICE']);
                $imageData = $productRow['IMAGE']->load();

                // Determine the image type based on the first few bytes of the image data
                $header = substr($imageData, 0, 4);
                $imageType = 'image/jpeg'; // default to JPEG

                if (strpos($header, 'FFD8') === 0) {
                    $imageType = 'image/jpeg'; // JPEG
                } elseif (strpos($header, '89504E47') === 0) {
                    $imageType = 'image/png'; // PNG
                }

                // Display each product in a card format
                echo "
                <div class='product-card'>
                    <a href='productdetail.php?product=" . urlencode($productName) . "'>
                        <img src='data:{$imageType};base64," . base64_encode($imageData) . "' alt='{$productName}'>
                    </a>
                    <h3>{$productName}</h3>
                    <p>£ {$productPrice}</p>
                    <div class='button-container'>";
                
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    // User is logged in
                    echo "
                    <button class='add-to-cart' onclick='addToCart(\"{$productName}\", {$productPrice})'>Add to Cart</button>
                    <button class='favorite' onclick='addToWishlist(\"{$productName}\", {$productPrice})'><i class='fas fa-heart'></i></button>";
                } else {
                    // User is not logged in
                    $productDetailUrl = 'productdetail.php?product=' . urlencode($productName); // URL to product detail page
                    echo "<a class='checkout-button' href='{$productDetailUrl}'>View Product</a>";
                }
                
                echo "
                    </div> <!-- Close button-container -->
                </div>"; // Close product-card
            }
        } else {
            $error_message = oci_error($productStmt);
            echo "Failed to execute product query: " . $error_message['message'];
        }

        // Free the statement and close the connection
        oci_free_statement($productStmt);
        oci_close($connection);
    } else {
        echo 'Invalid shop selection.';
    }
    ?>
</div>
</div>



<?php
include("footer.php");
?>

  </body>