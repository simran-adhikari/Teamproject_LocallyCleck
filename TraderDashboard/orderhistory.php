
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/trader.css">
    <title>Order History</title>
    <link rel="icon" type="image/x-icon" href="../resource/logo.png" alt="Logo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .order-history {
            margin: 20px;
            margin-left: 250px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #ff9800;
        }
        
        tr {
            background-color: #fbf6ee;
        }
    </style>
</head>
<body>
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
include("nav/header.php");
include("nav/sidenav.php");
?>
<!-- Order History -->
<div class="order-history">
    <h2>Order History</h2>
    <table>
        <thead>
            <tr>
                <th>Shop Name</th>
                <th>Customer Name</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Order ID</th>
                <th>Total Payment</th> <!-- Added Total Payment column -->
            </tr>
        </thead>
        <tbody>
            <?php
                include("../connect.php");


                $trader_id = $_SESSION['trader_id'];

                // Prepare and execute statement
                $query ="
                SELECT s.SHOP_NAME, c.first_name, p.PRODUCT_NAME, op.QUANTITY, od.order_id,
                       (p.product_price * op.quantity * (1 - NVL(o.offer_percentage, 0) / 100)) AS TOTAL_PAYMENT
                FROM orderdetail od
                JOIN order_product op ON od.order_id = op.order_id
                JOIN product p ON op.product_id = p.product_id
                JOIN shop s ON p.shop_id = s.shop_id
                JOIN customer c ON od.customer_id = c.customer_id
                LEFT JOIN offer o ON p.offer_id = o.offer_id
                WHERE s.shop_id = :trader_id";
                
                $stmt = oci_parse($connection, $query);
                oci_bind_by_name($stmt, ":trader_id", $trader_id);
                oci_execute($stmt);
                
                // Fetch and display data
                while ($row = oci_fetch_assoc($stmt)) {
                    echo "<tr>\n";
                    echo "<td>" . $row['SHOP_NAME'] . "</td>\n";
                    echo "<td>" . $row['FIRST_NAME'] . "</td>\n"; 
                    echo "<td>" . $row['PRODUCT_NAME'] . "</td>\n";
                    echo "<td>" . $row['QUANTITY'] . "</td>\n";
                    echo "<td>" . $row['ORDER_ID'] . "</td>\n";
                    echo "<td>" . number_format($row['TOTAL_PAYMENT'], 2) . "</td>\n"; // Display Total Payment
                    echo "</tr>\n";
                }
                
                oci_free_statement($stmt);
                oci_close($connection);
            ?>
        </tbody>
    </table>
</div>
</body>
</html>


