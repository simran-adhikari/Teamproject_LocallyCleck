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
    <title>Locally Cleck</title>
    <link rel="icon" type="image/x-icon" href="resource/logo.png" alt="Logo">
</head>
<body>

<?php
include("header.php");
?>

<div class="section1">
    <?php
    // Display trader box
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
        echo '<a href="trader_details.php?shop_name=' . urlencode($shopName) . '"><img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . htmlspecialchars($shopName) . '"></a>';
        echo '<span class="trader-name">' . htmlspecialchars($shopName) . '</span>';
        echo '</div>';
    }
    
    echo '</div>';
    ?>

    <div class="banner-container">
        <div class="banner">
            <img src="resource/Home/banner1.jpg" class="banner-slide">
            <img src="resource/Home/banner2.jpg" class="banner-slide">
            <img src="resource/Home/banner4.jpg" class="banner-slide">
            <img src="resource/Home/banner3.jpg" class="banner-slide">
        </div>
    </div>
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
    <h2>Best Sellers</h2>
    <div class="product-list">
        <?php
        // Establish a new SQL query to fetch products from the Product table
        $productSql = "SELECT 
                        p.image AS Image,
                        p.product_name AS Product_Name,
                        p.product_price AS Product_Price,
                        c.category_name AS Category_Name,
                        op.order_count AS Order_Count
                      FROM 
                        (SELECT 
                            product_id, 
                            COUNT(product_id) AS order_count
                         FROM 
                            Order_Product
                         GROUP BY 
                            product_id
                         ORDER BY 
                            order_count DESC
                        ) op
                      JOIN 
                        Product p ON op.product_id = p.product_id
                      JOIN 
                        Category c ON p.category_id = c.category_id
                      WHERE 
                        p.isVerified = 'Y'
                      AND 
                        ROWNUM <= 4
                        AND 
                        p.quantity > 2
                      ORDER BY 
                        op.order_count DESC";

        // Prepare the SQL statement
        $productStmt = oci_parse($connection, $productSql);

        // Execute the SQL statement
        oci_execute($productStmt);

        // Loop through each row of the result set
        while ($productRow = oci_fetch_assoc($productStmt)) {
            $productName = $productRow['PRODUCT_NAME'];
            $productPrice = $productRow['PRODUCT_PRICE'];
            $imageData = $productRow['IMAGE']->load();
            $category = $productRow['CATEGORY_NAME'];

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
            echo "
            <div class='product-card'>
                <a href='productdetail.php?product=" . urlencode($productName) . "'>
                    <img src='data:{$imageType};base64,{$encodedImageData}' alt='" . htmlspecialchars($productName) . "'>
                </a>
                <h3>" . htmlspecialchars($productName) . "</h3>
                <h7>" . htmlspecialchars($category) . "</h7>
                <p>£ " . htmlspecialchars($productPrice) . "</p>
                <div class='button-container'>
                    <button class='add-to-cart' onclick='addToCart(\"" . addslashes($productName) . "\", " . htmlspecialchars($productPrice) . ")'>Add to Cart</button>
                    <button class='favorite' onclick='addToWishlist(\"" . addslashes($productName) . "\", " . htmlspecialchars($productPrice) . ")'><i class='fas fa-heart'></i></button>
                </div>
            </div>";
        }
        ?>
    </div>
</div>

<br><br>

<h2>Check-Out These</h2>
<div class="best-sellers">
    <div class="sort-by-icon">
        <span>Sort by:</span>
        <select id="sort-options" name="sort-options" onchange="sortProducts()">
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="name_asc">Name: A to Z</option>
            <option value="name_desc">Name: Z to A</option>
        </select>
    </div>
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
            case 'name_asc':
                $orderBy = "Product_Name ASC";
                break;
            case 'name_desc':
                $orderBy = "Product_Name DESC";
                break;
            default:
                $orderBy = "Product_Price ASC";
        }

        $productSql = "SELECT Image, Product_Name, Product_Price, c.category_name 
                       FROM Product
                       JOIN Category c on c.category_id = product.category_id
                       WHERE isVerified = 'Y'
                       AND product_id < 14
                       and quantity>2
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
            $category = $productRow['CATEGORY_NAME'];

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
            echo "
            <div class='product-card'>
                <a href='productdetail.php?product=" . urlencode($productName) . "'>
                    <img src='data:{$imageType};base64,{$encodedImageData}' alt='" . htmlspecialchars($productName) . "'>
                </a>
                <h3>" . htmlspecialchars($productName) . "</h3>
                <h7>" . htmlspecialchars($category) . "</h7>
                <p>£ " . htmlspecialchars($productPrice) . "</p>
                <div class='button-container'>
                    <button class='add-to-cart' onclick='addToCart(\"" . addslashes($productName) . "\", " . htmlspecialchars($productPrice) . ")'>Add to Cart</button>
                    <button class='favorite' onclick='addToWishlist(\"" . addslashes($productName) . "\", " . htmlspecialchars($productPrice) . ")'><i class='fas fa-heart'></i></button>
                </div>
            </div>";
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
    // Banner slider script
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

    // Sorting products script
    function sortProducts() {
        const sortOption = document.getElementById('sort-options').value;
        window.location.href = `?sort=${sortOption}`;
    }
</script>
</body>
</html>
