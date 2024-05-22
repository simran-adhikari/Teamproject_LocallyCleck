<?php
// Establish database connection
include "connect.php";

// Query to fetch product names, prices, and images
$query = "SELECT product_name, product_price, image FROM Product";
$statement = oci_parse($connection, $query);
oci_execute($statement);

$products = [];

// Fetch data and build products array
while ($row = oci_fetch_assoc($statement)) {
    $product = [
        "name" => $row['PRODUCT_NAME'],
        "price" => $row['PRODUCT_PRICE'],
    ];

    // Handle image data
    $imageData = $row['IMAGE']->load();
    $encodedImageData = base64_encode($imageData);

    $header = substr($imageData, 0, 4);
    $imageType = 'image/jpeg'; // default to JPEG

    if (strpos($header, 'FFD8') === 0) {
        $imageType = 'image/jpeg'; // JPEG
    } elseif (strpos($header, '89504E47') === 0) {
        $imageType = 'image/png'; // PNG
    }

    // Construct image data for card display
    $product["image"] = "data:" . $imageType . ";base64," . $encodedImageData;

    // Add product to products array
    $products[] = $product;
}

// Close connection and free statement
oci_free_statement($statement);
oci_close($connection);

// Return products data as JSON
echo json_encode($products);
?>
