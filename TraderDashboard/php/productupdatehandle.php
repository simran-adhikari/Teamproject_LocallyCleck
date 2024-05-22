<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

</body>
<?php
include("../../connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $allergyInfo = $_POST['allergyInfo'];
    $category = $_POST['category'];
    $maxOrder = $_POST['maxOrder'];
    $minOrder = $_POST['minOrder'];
    $offerType = $_POST['offerType'];

    // Prepare and execute the update query
    $updateQuery = "UPDATE Product 
                    SET product_name = :productName,
                        product_price = :price,
                        description = :description,
                        quantity = :quantity,
                        allergy_information = :allergyInfo,
                        category_id = :category,
                        max_order = :maxOrder,
                        min_order = :minOrder,
                        offer_id = :offerType,
                        isverified='N'
                    WHERE product_id = :product_id";

    $stmt = oci_parse($connection, $updateQuery);
    oci_bind_by_name($stmt, ":productName", $productName);
    oci_bind_by_name($stmt, ":price", $price);
    oci_bind_by_name($stmt, ":description", $description);
    oci_bind_by_name($stmt, ":quantity", $quantity);
    oci_bind_by_name($stmt, ":allergyInfo", $allergyInfo);
    oci_bind_by_name($stmt, ":category", $category);
    oci_bind_by_name($stmt, ":maxOrder", $maxOrder);
    oci_bind_by_name($stmt, ":minOrder", $minOrder);
    oci_bind_by_name($stmt, ":offerType", $offerType);
    oci_bind_by_name($stmt, ":product_id", $product_id);

    $result = oci_execute($stmt);
    if ($result) {
        // Output SweetAlert2 notification
        echo '<script type="text/javascript">';
        echo 'Swal.fire({';
        echo '  icon: "success",';
        echo '  title: "Product Updated",';
        echo '  showConfirmButton: false,';
        echo '  timer: 1000';
        echo '}).then(() => {';
        echo '  window.location.href = "../dashProduct.php";';
        echo '});';
        echo '</script>';
    } else {
        echo "Error updating product.";
        // Handle error scenario
    }
}

oci_close($connection);
?>