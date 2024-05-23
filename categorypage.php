<?php
session_start();
include("connect.php");

// Fetch category_id from the URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$categoryname = 'Blank'; // Default value

if ($category_id > 0) {
    // Prepare the SQL query to fetch the category name
    $sql = "SELECT CATEGORY_NAME FROM Category WHERE CATEGORY_ID = :category_id";
    $statement = oci_parse($connection, $sql);

    // Bind the category_id parameter
    oci_bind_by_name($statement, ":category_id", $category_id);

    // Execute the statement
    oci_execute($statement);

    // Fetch the category name
    if ($row = oci_fetch_assoc($statement)) {
        $categoryname = $row["CATEGORY_NAME"];
    } else {
        // Handle the case where the category_id does not match any category
        echo "Category not found.";
        exit;
    }

    // Free the statement
    oci_free_statement($statement);
} else {
    // Handle the case where category_id is not valid
    echo "Invalid category ID.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="icon" type="image/x-icon" href="resource/logo.png" alt="Logo">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/category.css" />
    <link rel="stylesheet" href="page.css" />
    <link rel="icon" href="shoes.png">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>


<body>
<style>
* {box-sizing: border-box;}
body {font-family: Verdana, sans-serif;}
.mySlides {display: none;}
img {vertical-align: middle;}

/* Slideshow container */
.slideshow-container {
  max-width: 100%;
  position: relative;
  margin: auto;
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active {
  background-color: #717171;
}

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 2s;
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .text {font-size: 11px}
}
</style>
</head>
<body>

<?php include("header.php"); 
include("functionalphp/toaster.php");

?>

<div class="slideshow-container">

<div class="mySlides fade">
  <img src="resource/Home/banner1.jpg" style="width:100%">
</div>

<div class="mySlides fade">
  <img src="resource/Home/banner2.jpg" style="width:100%">
</div>

<div class="mySlides fade">
  <img src="resource/Home/banner3.jpg" style="width:100%">
</div>

</div>
<br>

<div style="text-align:center">
  <span class="dot"></span> 
  <span class="dot"></span> 
  <span class="dot"></span> 
</div>

<script>
let slideIndex = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}    
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
  setTimeout(showSlides, 5000); // Change image every 2 seconds
}
</script>


    </div>
    <div class="best-sellers">
    <h2>Check-Out <?php echo $categoryname ?> Products</h2>
    <div class="product-list">
        <?php
        // Fetch the category_id from the URL
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

        // Establish a new SQL query to fetch products from the Product table for the given category_id
        $productSql = "SELECT Image, Product_Name, Product_Price FROM Product WHERE Category_ID = :category_id
         and isVerified='Y'
         ORDER BY Product_Price DESC";

        // Prepare the SQL statement
        $productStmt = oci_parse($connection, $productSql);

        // Bind the category_id parameter to the SQL query
        oci_bind_by_name($productStmt, ':category_id', $category_id);

        // Execute the SQL statement
        oci_execute($productStmt);

        // Loop through each row of the result set
        while ($productRow = oci_fetch_assoc($productStmt)) {
            $productName = $productRow['PRODUCT_NAME'];
            $productPrice = $productRow['PRODUCT_PRICE'];
            $imageData = $productRow['IMAGE']->load();

            // Encode the BLOB data as base64
            $encodedImageData = base64_encode($imageData);

            // Determine the image type based on the first few bytes of the image data
            $header = bin2hex(substr($imageData, 0, 4));
            $imageType = 'image/jpeg'; // default to JPEG

            if (strpos($header, 'ffd8') === 0) {
                $imageType = 'image/jpeg'; // JPEG
            } elseif (strpos($header, '89504e47') === 0) {
                $imageType = 'image/png'; // PNG
            }

            // Display each product in a card format
            echo '<div class="product-card">';
            echo '<a href="productdetail.php?product=' . urlencode($productName) . '">';
            echo '<img src="data:' . $imageType . ';base64,' . $encodedImageData . '" alt="' . $productName . '">';
            echo '</a>'; // Close the <a> tag for the images
            echo '<h3>' . $productName . '</h3>';
            echo '<p>£ ' . $productPrice . '</p>';

            echo '<div class="button-container">';

            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                // User is logged in
                echo '<button class="add-to-cart" onclick="addToCart(\'' . $productName . '\', ' . $productPrice . ')">Add to Cart</button>';
                echo '<button class="favorite" onclick="addToWishlist(\'' . $productName . '\', ' . $productPrice . ')"><i class="fas fa-heart"></i></button>';
            } else {
                // User is not logged in
                $productDetailUrl = 'productdetail.php?product=' . urlencode($productName); // URL to product detail page
                echo '<a class="checkout-button" href="' . $productDetailUrl . '">Checkout Product</a>';
            }

            echo '</div>'; // Close button-container
            echo '</div>'; // Close product-card
        }
        ?>
    </div>    </div>    


<?php include("footer.php"); ?>

</body>
</html>
<?php oci_close($connection); ?>