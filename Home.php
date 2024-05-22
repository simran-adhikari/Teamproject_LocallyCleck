<?php
session_start();

include("functionalphp/toaster.php");

// Establish database connection
include "connect.php";
// SQL query to fetch shop_name and profileimage from Shop table
$sql = "SELECT shop_name, profileimage 
FROM Shop 
JOIN Trader ON Shop.trader_id = Trader.trader_id 
WHERE Trader.isVerified = 'Y'";

// Prepare the SQL statement
$stmt = oci_parse($connection, $sql);

// Execute the SQL statement
oci_execute($stmt);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <link rel="stylesheet" href="css/home.css" />
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
  include("header.php");
  ?>

  
<div class="section1">
    <?php
         echo '<div class="trader-box">';

         // Loop through each row of the result set
         while ($row = oci_fetch_assoc($stmt)) {
             $shopName = $row['SHOP_NAME'];
             $imageData = $row['PROFILEIMAGE']->load();
 
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
 
             // Display each trader with the image
             echo '<div class="trader">';
             echo '<a href="trader_details.php?shop_name=' . urlencode($shopName) . '"><img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $shopName . '"></a>';
             echo '<span class="trader-name">' . $shopName . '</span>';
             echo '</div>';
         }
 
         echo '</div>';

?>

  <!--     <div class="banner"> -->

        <div class="banner-container">
       
            <div class="banner">
                <img src="resource/Home/banner1.jpg" class="banner-slide">
                <img src="resource/Home/banner2.jpg" class="banner-slide">
                <img src="resource/Home/banner4.jpg" class="banner-slide">
                <img src="resource/Home/banner3.jpg" class="banner-slide">
               

            </div>
        </div>
<!--   </div> -->
      </div>
        
      <div class="image-section">
        <div class="image-box"><a href="categorypage.php?category_id=4" data-text="Baker"><img src="resource/pic/bakery.jpg" alt="Baker"></a></div>
        <div class="image-box"><a href="categorypage.php?category_id=2" data-text="Green Grocer"><img src="resource/pic/veg.jpg" alt="Green Grocer"></a></div>
        <div class="image-box"><a href="categorypage.php?category_id=3" data-text="FishMonger"><img src="resource/pic/fish.jpg" alt="FishMonger"></a></div>
        <div class="image-box"><a href="categorypage.php?category_id=1" data-text="Butcher"><img src="resource/pic/meat.jpg" alt="Butcher"></a></div>
        <div class="image-box"><a href="categorypage.php?category_id=5" data-text="Delicatessen"><img src="resource/pic/deli.jpg" alt="Delicatessen"></a></div>
    </div>



<!-- Best Product Section -->

<div class="best-sellers">
    <h2>HOT Sellers</h2>
    <div class="product-list">
        <?php
        // Establish a new SQL query to fetch products from the Product table
        $productSql = "SELECT Image, Product_Name, Product_Price,c.category_name FROM Product 
         JOIN Category c on c.category_id=product.category_id
        WHERE isVerified='Y'
        and product_id < 4
         ORDER BY Product_Price DESC";

        // Prepare the SQL statement
        $productStmt = oci_parse($connection, $productSql);

        // Execute the SQL statement
        oci_execute($productStmt);

        // Loop through each row of the result set
        while ($productRow = oci_fetch_assoc($productStmt)) {
            $productName = $productRow['PRODUCT_NAME'];
            $productPrice = $productRow['PRODUCT_PRICE'];
            $imageData = $productRow['IMAGE']->load();
            $category= $productRow['CATEGORY_NAME'];

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
// Display each product in a card format
echo '<div class="product-card">';
echo '<a href="productdetail.php?product=' . urlencode($productName) . '">';
echo '<img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $productName . '">';
echo '</a>'; // Close the <a> tag for the images
echo '<h3>' . $productName . '</h3>';
echo '<h7>' . $category . '</h7>';
echo '<p>£ ' . $productPrice . '</p>';

echo '<div class="button-container">';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    // User is logged in
    
    echo '<button class="add-to-cart" onclick="addToCart(\'' . $productName . '\', ' . $productPrice . ')">Add to Cart</button>';
    echo '<button class="favorite" onclick="addToWishlist(\'' . $productName . '\', ' . $productPrice . ')"><i class="fas fa-heart"></i></button>';
} 
else {
    // User is not logged in
    $productDetailUrl = 'productdetail.php?product=' . urlencode($productName); // URL to product detail page
    echo '<a class="checkout-button" href="' . $productDetailUrl . '">Checkout Product</a>';
}

echo '</div>'; // Close button-container
echo '</div>'; // Close product-card
        }
?>
    </div>
</div>


<br>
<br>


<div class="sort-by">
        <label for="sort-options">Sort by:</label>
        <select id="sort-options" name="sort-options" onchange="sortProducts()">
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
        </select>
</div>

<div class="best-sellers">
    <h2>Other Products</h2>
    <div class="product-list">
        <?php
        // Establish a new SQL query to fetch products from the Product table
        $sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
        $orderBy = "";

        switch ($sortOption) {
            case 'price_asc':
                $orderBy = "Product_Price ASC";
                break;
            case 'price_desc':
                $orderBy = "Product_Price DESC";
                break;
            default:
                $orderBy = "Product_Price ASC";
        }

        $productSql = "SELECT Image, Product_Name, Product_Price,c.category_name FROM Product
        JOIN Category c on c.category_id=product.category_id
         WHERE isVerified='Y'
        and product_id>4
         ORDER BY $orderBy";

        // Prepare the SQL statement
        $productStmt = oci_parse($connection, $productSql);

        // Execute the SQL statement
        oci_execute($productStmt);

        // Loop through each row of the result set
        while ($productRow = oci_fetch_assoc($productStmt)) {
            $productName = $productRow['PRODUCT_NAME'];
            $productPrice = $productRow['PRODUCT_PRICE'];
            $imageData = $productRow['IMAGE']->load();
            $category= $productRow['CATEGORY_NAME'];

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

            // Display each product in a card format
            echo '<div class="product-card">';
            echo '<a href="productdetail.php?product=' . urlencode($productName) . '">';
            echo '<img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $productName . '">';
            echo '</a>'; // Close the <a> tag for the images
            echo '<h3>' . $productName . '</h3>';
            echo '<h7>' . $category . '</h7>';
            echo '<p>£ ' . $productPrice . '</p>';
            echo '<div class="button-container">';
            echo '<button class="add-to-cart" onclick="addToCart(\'' . $productName . '\', ' . $productPrice . ')">Add to Cart</button>';
            echo '<button class="favorite" onclick="addToWishlist(\'' . $productName . '\', ' . $productPrice . ')"><i class="fas fa-heart"></i></button>';
            echo '</div>'; // Close button-container
            echo '</div>'; // Close product-card
        }
        ?>
    </div>
</div>

<br>

<?php
include("footer.php");
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        const slides = document.getElementsByClassName("banner-slide");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 2500); 
    }
});
</script>
<script>
    function sortProducts() {
        const sortOption = document.getElementById('sort-options').value;
        window.location.href = `?sort=${sortOption}`;
    }
</script>


  </body>
</html>
