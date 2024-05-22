<?php 
session_start();
include("../connect.php");

if (!isset($_SESSION['trader_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: ../Home.php");
    exit();
}
$trader_id = $_SESSION['trader_id'];


// Fetch reviews from OCI database
$reviewSql = "SELECT R.review_id, P.product_name, C.first_name, R.review_comment
            FROM Review R
            INNER JOIN Product P ON R.product_id = P.product_id
            INNER JOIN Shop S ON P.shop_id = S.shop_id
            INNER JOIN Customer C ON R.customer_id = C.customer_id
            WHERE S.trader_id = :trader_id";

// Prepare and execute the statement
$reviewStatement = oci_parse($connection, $reviewSql);
oci_bind_by_name($reviewStatement, ":trader_id", $trader_id);
oci_execute($reviewStatement);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo.png">
    <link rel="stylesheet" href="css/traderdashboardcss.css">
    <title>Product Reviews</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <style>
        

        .review-text {
  font-size: 16px;
  line-height: 1.5;
}

.product-name, .customer-name, .review-comment {
  color: #ff9800;
}


.customer-review {
      background-color: #fbf6ee; 
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      margin: 20px;
   margin-left: 250px;
  padding: 20px;
  border-radius: 5px;
  margin-bottom: 20px;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow on hover */
  
    
    }

    .product-container{
        margin: 20px;
   margin-left: 250px;
   
    }

  


</style>
</head>
<body>
<?php

if (isset($_SESSION['display_name'])) {
    $traderName = $_SESSION['display_name'];
} else {
    $traderName='Trader';
}
include("nav/header.php");
include("nav/sidenav.php");
?>

<!-- Product display section -->
<div class="product-container">
<h2>Customer Reviews</h2>
</div>



    
    <?php
    // Loop through the fetched reviews and display them
    while ($row = oci_fetch_assoc($reviewStatement)) {
        echo '<div class="customer-review">';
        echo '<div class="review-text">';
        echo '<p><strong class="product-name">Product Name: </strong>' . htmlspecialchars($row['PRODUCT_NAME']) . '</p>';
        echo '<p><strong class="customer-name">Customer Name: </strong>' . htmlspecialchars($row['FIRST_NAME']) . '</p>';
        echo '<p><strong class="review-comment">Review Comment: </strong>' . htmlspecialchars($row['REVIEW_COMMENT']) . '</p>';
       echo '</div>'; // Close review-text div
        echo '</div>'; // Close customer-review div
    }
    ?>


<?php
// Free the statement
oci_free_statement($reviewStatement);

// Close the database connection
oci_close($connection);
?>



</body>
</html>
