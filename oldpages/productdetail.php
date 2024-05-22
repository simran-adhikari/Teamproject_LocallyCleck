<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
    <title>Document</title>
  </head>
  <body>

  <?php
include("header.php");
include("functionalphp/toaster.php");

?>

<br>
<br>

<?php
include "connect.php"; // Assuming this file contains the database connection logic
$product_name = $_GET['product'];

// SQL query to fetch product details
$sql = "SELECT product_id,Product_Name, PRODUCT_PRICE, DESCRIPTION, ALLERGY_INFORMATION, IMAGE , c.category_name
        FROM Product
        JOIN Category c on c.category_id=product.category_id
        WHERE Product_Name = :product_name";

// Prepare the SQL statement
$statement = oci_parse($connection, $sql);

// Bind the parameter ':product_name' with the actual product name
oci_bind_by_name($statement, ":product_name", $product_name);

// Execute the SQL statement
if (oci_execute($statement)) {
    // Fetch each row from the result set
    while ($row = oci_fetch_assoc($statement)) {
        $product_id= $row['PRODUCT_ID'];
        $product_name = $row['PRODUCT_NAME'];
        $product_price = $row['PRODUCT_PRICE'];
        $description = $row['DESCRIPTION'];
        $allergy_info = $row['ALLERGY_INFORMATION'];
        $image_blob = $row['IMAGE'];
        $category= $row['CATEGORY_NAME'];


        // Process BLOB data (convert to base64 for embedding in HTML)
        $image_data = $image_blob->load();
        $image_base64 = base64_encode($image_data);

        // Display product details using fetched data
        echo '<div class="product-container">';
        echo '<div class="product-header">';
        echo '<h1 class="product-name">' . $product_name . '</h1>';
        echo '</div>';
        echo '<div class="product-content">';
        echo '<div class="product-image">';
        echo '<img src="data:image/jpeg;base64,' . $image_base64 . '" alt="Product Image">';
        echo '<p class="product-price">£ ' . $product_price . '</p>';
        echo '</div>';
        echo '<div class="product-details">';
        echo '<p class="product-description">' . $description . '</p>';
        echo '<p class="allergy-info"><em>' . $allergy_info . '</em></p>';
        echo '<p class="allergy-info"><em> Category: ' . $category . '</em></p>';

        echo '<div class="product-actions">';
        echo '<div class="add-to-cart">';
        echo '<button class="cart-button" onclick="addToCart(\'' . $product_name . '\')">Add to Cart</button>';
        echo '</div>';
        echo '<div class="heart-icon">';
        echo '<i class="fas fa-heart"  onclick="addToWishlist(\'' . $product_name . '\')"></i>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    $error_message = oci_error($statement);
    echo "Failed to execute SQL: " . $error_message['message'];
}

// Free the statement and close the connection
oci_free_statement($statement);
oci_close($connection);
?>


<!-- Similat Product Section -->

<?php

$connection = oci_connect("diwon", "hackvayo", "//localhost/xe");

if (!$connection) {
    $error_message = oci_error();
    echo "Failed to connect to Oracle: " . $error_message['message'];
    exit();
}

// SQL query to fetch similar products (limit to 3 for demonstration) update this with category
$sql = "SELECT image, product_name 
FROM Product 
WHERE category_id = (SELECT category_id 
                     FROM Product 
                     WHERE product_name = :product_name) 
  AND ROWNUM <= 3";

// Prepare and execute the query
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ":product_name", $product_name);
oci_execute($stmt);

// Display similar products section
echo '<div class="similar-products-section">';
echo '<h2 class="section-title">Similar Products</h2>';
echo '<div class="similar-products-container">';

// Loop through the fetched rows and display each product
while (($row = oci_fetch_assoc($stmt)) !== false) {
    echo '<div class="similar-product">';
    
    // Display image (assuming the image is stored as a BLOB)
    // Read the BLOB data and convert it to base64 for inline display
    $image_data = $row['IMAGE']->load();
    $image_base64 = base64_encode($image_data);
    $productName= $row['PRODUCT_NAME'];
    echo '<a href="productdetail.php?product=' . urlencode($productName) . '">';
    echo '<img src="data:image/jpeg;base64,' . $image_base64 . '" alt="Similar Product">';
    echo '</a>'; // Close the <a> tag for the images
    
    echo '<p class="similar-product-name">' . htmlspecialchars($row['PRODUCT_NAME']) . '</p>';
    echo '<button class="heart-button" onclick="addToWishlist(\'' . $productName . '\')"><i class="far fa-heart"></i></button>';
    
    echo '</div>'; // Close similar-product
}

echo '</div>'; // Close similar-products-container
echo '</div>'; // Close similar-products-section

// Clean up
oci_free_statement($stmt);
oci_close($connection);
?>


<!-- User Reviews -->
<?php
// Database connection
include('connect.php');

$product_name = $_GET['product'];
// Query to fetch reviews along with customer details
$query = "SELECT r.REVIEW_COMMENT, c.FIRST_NAME, c.LAST_NAME
          FROM REVIEW r
          JOIN CUSTOMER c ON r.CUSTOMER_ID = c.CUSTOMER_ID
          WHERE r.PRODUCT_ID = (SELECT product_id FROM Product WHERE product_name = :product_name)";

$stmt = oci_parse($connection, $query);
oci_bind_by_name($stmt, ":product_name", $product_name);
oci_execute($stmt);

// Display user reviews
echo '<div class="user-reviews-section">';
echo '<h2 class="reviewh2">User Reviews</h2>';
echo '<div class="user-reviews-container">';

while ($row = oci_fetch_assoc($stmt)) {
    echo '<div class="user-review-card">';
    echo '<img src="resource/usericon.jpg" alt="User Icon" class="user-icon">';
    echo '<div class="user-details">';
    echo '<span class="username">' . $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'] . '</span>';
    echo '<p class="review-text">' . $row['REVIEW_COMMENT'] . '</p>';
    echo '</div>'; // Close user-details
    echo '</div>'; // Close user-review-card
}

echo '</div>'; // Close user-reviews-container
echo '</div>'; // Close user-reviews-section

// Close database connection
oci_close($connection);
?>



<!-- Add Review -->

<?php

if (isset($_SESSION['logged_in'])) {
?>
    <div class="review-form-container">
        <h2 class="reviewh2">Write a Review</h2>
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


<?php
} // End of logged-in check
?>


<?php
include("footer.php");
?>





<script>
  document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('reviewForm');

    var submitButton = document.getElementById('submitReviewBtn');
    submitButton.addEventListener('click', function(event) {
        event.preventDefault();

        var formData = new FormData(form);
        formData.append('productId', document.getElementById('productId').value);

        fetch('functionalphp/submitreview.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            alert('Review submitted successfully!');
            setTimeout(function() {
                window.location.reload();
            }, 10);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error submitting review.');
        });
    });
});
</script>
    </body>
</html>