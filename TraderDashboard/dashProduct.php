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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Document</title>
    
</head>
<body class="montserrat-text">
<?php
include("nav/header.php");
?>

    <!-- Dashboard Menu -->

<?php
include("nav/sidenav.php");
?>

     <!--Products-->
     <?php

// Check if a delete request is made
if (isset($_POST['delete_product_id'])) {
    $product_id = $_POST['delete_product_id'];

    include("connect.php");
    // Prepare and execute query to delete the product
    $query = "DELETE FROM Product WHERE product_id = :product_id";

    $statement = oci_parse($connection, $query);
    oci_bind_by_name($statement, ":product_id", $product_id);

    if (oci_execute($statement)) {
        echo "Product deleted successfully.";
    } else {
        $error_message = oci_error($statement);

        //We need to handel the error
    }

    oci_free_statement($statement);
    oci_close($connection);
}

// Establish a new connection to retrieve products

include("../connect.php");

$shopis=$_SESSION['trader_id'];
// Prepare and execute query to fetch products based on session user's shop_id
$query = "SELECT * FROM Product WHERE shop_id =:shopis";
$statement = oci_parse($connection, $query);
oci_bind_by_name($statement, ":shopis", $shopis);


if (oci_execute($statement)) {
    echo '<div class="product-page">';
    echo '<a href="dashAdd.php"> <button class="add-product-btn">Add Product</button> </a>';
    while ($row = oci_fetch_assoc($statement)) {
        echo '<div class="product-item">';
        echo '<img src="data:image/jpeg;base64,' . base64_encode($row['IMAGE']->load()) . '" alt="' . $row['PRODUCT_NAME'] . '">';
        echo '<div class="product-details">';
        echo '<h3>' . $row['PRODUCT_NAME'] . '</h3>';
        echo '<p>Price: £ ' . number_format($row['PRODUCT_PRICE'], 2) . '</p>';
        // Add other product details as needed
        echo '</div>';
       // echo '<form method="post">';
     //   echo '<input type="hidden" name="delete_product_id" value="' . $row['PRODUCT_ID'] . '">';
        echo '    <button class="remove-btn"><i class="fa-solid fa-trash-can"></i> Delete</button>        ';
       // echo '</form>';
       echo '<a href="updateProduct.php?product_id=' . $row['PRODUCT_ID'] . '" class="update-btn">';
       echo '<i class="fa-solid fa-edit"></i> Update';
       echo '</a>';
   
       echo '</div>';
    }
    echo '</div>';
} else {
    $error_message = oci_error($statement);
    echo "Failed to execute query: " . $error_message['message'];
}

oci_free_statement($statement);
oci_close($connection);
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JavaScript -->
    <script>
        // Select the button
        const button = document.querySelector('.remove-btn');

        // Add event listener to the button
        button.addEventListener('click', function() {
            // Show SweetAlert2 message
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                html: 'Product Exists in Customer Order<br>Contact System Admin to Delete Product!'
            });
        });
    </script>
</body>
</html>
