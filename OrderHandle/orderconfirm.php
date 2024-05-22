<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding-top: 50px;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #28a745;
        }
        p {
            color: #333333;
            margin-bottom: 20px;
        }
        .logo-container {
            width: 100px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 50%;
            overflow: hidden;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
        }
        .payment-details {
            font-weight: bold;
            color: #333333;
        }
        a {
            display: inline-block;
            text-decoration: none;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
        <img src="../resource/logo.png" alt="Logo">
        </div>
        <h2>Order Placed Successfully</h2>
        <p>Your order has been successfully placed!</p>
        <?php
        // Retrieve orderTotal from URL parameter
        $orderTotal = isset($_GET['orderTotal']) ? floatval($_GET['orderTotal']) : 0.00;
        ?>
        <p class="payment-details">Total Payment: $<?php echo number_format($orderTotal, 2); ?></p>
        <a href="../customerorder.php">Back to Home</a>
    </div>
</body>
</html>