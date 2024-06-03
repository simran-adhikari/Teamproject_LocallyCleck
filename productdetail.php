<?php
include("connect.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Card</title>
  <link rel="icon" type="image/x-icon" href="images/logo.png">
  <link rel="stylesheet" href="css/productdetail.css" />
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
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <?php 
include("header.php");
?>

  
</head>
<body>


<?php
include "connect.php"; // Assuming this file contains the database connection logic
$product_name = $_GET['product'];

// SQL query to fetch product details
$sql = "SELECT product_id, Product_Name, PRODUCT_PRICE, DESCRIPTION, ALLERGY_INFORMATION, IMAGE, quantity, c.category_name
        FROM Product
        JOIN Category c on c.category_id = product.category_id
        WHERE Product_Name = :product_name";

// Prepare the SQL statement
$statement = oci_parse($connection, $sql);

// Bind the parameter ':product_name' with the actual product name
oci_bind_by_name($statement, ":product_name", $product_name);

// Execute the SQL statement
if (oci_execute($statement)) {
    // Fetch each row from the result set
    while ($row = oci_fetch_assoc($statement)) {
        $product_id = $row['PRODUCT_ID'];
        $product_name = $row['PRODUCT_NAME'];
        $product_price = $row['PRODUCT_PRICE'];
        $description = $row['DESCRIPTION'];
        $allergy_info = $row['ALLERGY_INFORMATION'];
        $image_blob = $row['IMAGE'];
        $category = $row['CATEGORY_NAME'];
        $product_quantity = $row['QUANTITY'];

        // Process BLOB data (convert to base64 for embedding in HTML)
        $image_data = $image_blob->load();
        $image_base64 = base64_encode($image_data);
        ?>
        
        <div class="product-container">
           
              
            <div class="product-content">
                <div class="product-image">
                    <img src="data:image/jpeg;base64,<?php echo $image_base64; ?>" alt="Product Image">
                    <p class="product-price">£<?php echo htmlspecialchars($product_price); ?></p>
                </div>
                <div class="product-details">
                <h1 class="product-name"><?php echo htmlspecialchars($product_name); ?></h1>
           
                    <p class="product-description"><?php echo htmlspecialchars($description); ?></p>
                    <p class="allergy-info"><em><?php echo htmlspecialchars($allergy_info); ?></em></p>
                    <p class="product-category"><em>Category: <?php echo htmlspecialchars($category); ?></em></p>

                    <div class="product-actions">
                        <div class="add-to-cart">
                            <button class="cart-button" onclick="addToCart('<?php echo addslashes($product_name); ?>')">Add to Cart</button>
                        </div>
                        <div class="heart-icon">
                            <i class="fas fa-heart" onclick="addToWishlist('<?php echo addslashes($product_name); ?>')"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }
} else {
    $error_message = oci_error($statement);
    echo "Failed to execute SQL: " . htmlspecialchars($error_message['message']);
}

// Free the statement and close the connection
oci_free_statement($statement);
oci_close($connection);
?>

<br>
<br>
 
<!--Similar Products -->
<!-- Similat Product Section -->

<?php

include "connect.php";



// SQL query to fetch similar products (limit to 3 for demonstration) update this with category
$sql = "SELECT image, product_name , product_price, c.category_name
FROM Product 
JOIN Category c on c.category_id=product.category_id
WHERE product.category_id = (SELECT category_id 
                     FROM Product 
                     WHERE product_name = :product_name) 
AND Product_Name != :product_name
AND ROWNUM <= 4";


$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ":product_name", $product_name);
oci_execute($stmt);

// Display similar products section
echo '<div class="similar-products-section">';
echo '<h2 class="section-title">Similar Products</h2>';
echo '<div class="similar-products-container">';

// Loop through the fetched rows and display each product
while (($row = oci_fetch_assoc($stmt)) !== false) {
    // Display image (assuming the image is stored as a BLOB)
    // Read the BLOB data and convert it to base64 for inline display
    $image_data = $row['IMAGE']->load();
    $image_base64 = base64_encode($image_data);
    $productName = $row['PRODUCT_NAME'];
    $categoryName = htmlspecialchars($row['CATEGORY_NAME']);
    $productPrice = htmlspecialchars($row['PRODUCT_PRICE']);
    $encodedProductName = urlencode($productName);
    $safeProductName = htmlspecialchars($productName);

    echo "
    <div class='similar-product'>
        <p class='similar-product-name'>{$safeProductName}</p>
        <a href='productdetail.php?product={$encodedProductName}'>
            <img src='data:image/jpeg;base64,{$image_base64}' alt='Similar Product'>
        </a>
        <p class='allergy-info'>{$categoryName}</p>
        <p class='similar-product-name'>£ {$productPrice}</p>
        <button class='heart-button' onclick='addToWishlist(\"" . addslashes($productName) . "\")'><i class='far fa-heart'></i></button>
    </div>";
}

echo '</div>'; // Close similar-products-container
echo '</div>'; // Close similar-products-section

// Clean up
oci_free_statement($stmt);
oci_close($connection);
?>


    

<h2 class="reviewh2">Customer Reviews</h2>

 
<?php
// Database connection
include('connect.php');

// Query to fetch reviews along with customer details
$query = "SELECT r.REVIEW_COMMENT, c.FIRST_NAME, c.LAST_NAME
          FROM REVIEW r
          JOIN CUSTOMER c ON r.CUSTOMER_ID = c.CUSTOMER_ID
          WHERE r.PRODUCT_ID = (SELECT product_id FROM Product WHERE product_name = :product_name)";



$stmt = oci_parse($connection, $query);
oci_bind_by_name($stmt, ":product_name", $product_name);
oci_execute($stmt);

// Display reviews

while ($row = oci_fetch_assoc($stmt)) {
    echo "
    <div class='customer-reviews-container'>
        <div class='customer-review'>
            <img src='resource/usericon.jpg' alt='User Icon' class='user-icon' width='50' height='50'>
            <div class='customer-name'>{$row['FIRST_NAME']} {$row['LAST_NAME']}</div>
            <p class='review-text'>{$row['REVIEW_COMMENT']}</p>
        </div>
    </div>";
}
echo '</div>';
?>

  <br>
<br>





<!-- Add Review -->
<?php
if (isset($_SESSION['logged_in'])) {
?>
    <div class="review-form-container">
        <h2 class="reviewh2">Review Product</h2>
        <form id="reviewForm" class="reviewform" action="functionalphp/submitreview.php" method="POST">
            <input type="hidden" id="productId" name="productId" value="<?php echo $product_id; ?>">
            <div class="review-input">
                <label for="userReview" class="review-label">Your Review:</label>
                <textarea id="userReview" name="userReview" rows="6" class="review-textarea" required></textarea>
            </div>
            <div class="review-submit">
                <button type="button" id="submitReviewBtn" class="review-button">Submit Review</button>
            </div>
        </form>
    </div>

    <!-- Toastify CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        document.getElementById('submitReviewBtn').addEventListener('click', function() {
            var form = document.getElementById('reviewForm');
            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(responseText => {
                if (responseText.includes("Review submitted successfully!")) {
                    Toastify({
                        text: "Review submitted successfully!",
                        duration: 1000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "green",
                        callback: function() {
                            location.reload();
                        }
                    }).showToast();
                } else if (responseText.includes("Error: You can only review products that you have purchased.")) {
                    Toastify({
                        text: "Error: Please buy the product to review it.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "red",
                    }).showToast();
                } else {
                    Toastify({
                        text: "Error submitting review.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "red",
                    }).showToast();
                }
            })
            .catch(error => {
                Toastify({
                    text: "Error submitting review.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "red",
                }).showToast();
            });
        });
    </script>
<?php
} // End of logged-in check
?>


<br>
<br>








  <script>
// Add event listeners to similar product elements
const similarProducts = document.querySelectorAll('.product-card[data-id]');
      similarProducts.forEach(product => {
        product.addEventListener('click', () => {
          // Retrieve product information
          const productId = product.getAttribute('data-id');
          const productName = product.querySelector('p').innerText;
          const productImageSrc = product.querySelector('img').getAttribute('src');
          const productAllergyInfo = product.querySelector('.allergy-info').innerText;

          // Construct URL with query parameters
          const urlParams = new URLSearchParams();
          urlParams.append('id', productId);
          urlParams.append('name', productName);
          urlParams.append('image', productImageSrc);
          urlParams.append('allergy', productAllergyInfo);
          const productDetailURL = `product_detail.php?${urlParams.toString()}`;

          // Redirect to the product detail page
          window.location.href = productDetailURL;
        });
      });

  </script>

    <?php 
    include("footer.php");
    include("functionalphp/toaster.php");
  ?>
</body>
</html>



    