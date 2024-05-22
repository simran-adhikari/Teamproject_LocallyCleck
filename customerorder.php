<?php
session_start();
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

    <title>Order History</title>
    <style>
        .order-container {
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .order-header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 8px 8px 0 0;
        }
        .order-body {
            padding: 10px;
        }
        .order-body p {
            margin: 5px 0;
        }
        .product-list {
            margin-top: 10px;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .product-list p {
            margin: 0;
        }
        .order-history-button {
            background-color: #28a745;
            color: white;
            border: 2px solid #218838;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .order-history-button:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
<?php
include("header.php");
?>
    <h1>Order History</h1>
    <?php
    // Connect to Oracle database
    include("connect.php");
    // Fetch customer ID (In a real application, you would get this from session or request)
    $customer_id =$_SESSION['user_id']; // For example purposes, we use a static customer ID

    // SQL query to fetch order details
    $order_query = "
        SELECT order_id, ORDER_DATE, ORDER_TOTAL, ORDER_TIME, ORDER_DAY
        FROM OrderDetail
        WHERE CUSTOMER_ID = :customer_id
        ORDER BY ORDER_DATE DESC
    ";

    // Prepare and execute the query
    $stid = oci_parse($connection, $order_query);
    oci_bind_by_name($stid, ":customer_id", $customer_id);
    oci_execute($stid);

    $order_found = false;

    // Fetch and display order details
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $order_found = true;
        $order_id = $row['ORDER_ID'];
        $order_date = $row['ORDER_DATE'];
        $order_total = $row['ORDER_TOTAL'];
        $order_time = $row['ORDER_TIME'];
        $order_day = $row['ORDER_DAY'];

        echo "<div class='order-container'>";
        echo "<div class='order-header'>Order ID: {$order_id}</div>";
        echo "<div class='order-body'>";
        echo "<p>Order Date: {$order_date}</p>";
        echo "<p>Order Total: £{$order_total}</p>";
        echo "<p>Order Time: {$order_time}</p>";
        echo "<p>Order Day: {$order_day}</p>";

        // Fetch product details for this order
        echo "<div class='product-list'><strong>Products:</strong><br>";
        $product_query = "
            SELECT P.PRODUCT_NAME, OP.QUANTITY
            FROM Order_Product OP
            JOIN Product P ON OP.PRODUCT_ID = P.PRODUCT_ID
            WHERE OP.ORDER_ID = :order_id
        ";

        $product_stid = oci_parse($connection, $product_query);
        oci_bind_by_name($product_stid, ":order_id", $order_id);
        oci_execute($product_stid);

        while ($product_row = oci_fetch_array($product_stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $product_name = $product_row['PRODUCT_NAME'];
            $quantity = $product_row['QUANTITY'];
            echo "<p>{$product_name} (Qty: {$quantity})</p>";
        }
        echo "</div>"; // Close product-list
        echo "</div>"; // Close order-body
        echo "</div>"; // Close order-container
    }

    if (!$order_found) {
        echo "<p>You are yet to make an order.</p>";
    }

    // Close the Oracle connection
    oci_free_statement($stid);
    if (isset($product_stid)) {
        oci_free_statement($product_stid);
    }
    oci_close($connection);
    ?>
</body>
</html>