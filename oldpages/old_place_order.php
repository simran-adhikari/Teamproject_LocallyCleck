<?php
include "connect.php"; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

session_start(); // Start the session if not already started

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['collection-day'])) {
    // Retrieve form data
    $userId = $_POST['user_id'];
    $slotId = $_POST['collection-day'];

    // Calculate order total from cart items
    $orderTotal = 0;

    // SQL query to fetch cart items and corresponding product details
    $sql = "SELECT p.Product_ID, p.Product_Price, cp.Quantity
            FROM product p
            JOIN cart_product cp ON p.Product_ID = cp.Product_ID
            WHERE cp.Cart_ID = :userId";

    // Prepare the SQL statement
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':userId', $userId);

    // Execute the SQL statement
    oci_execute($stmt);

    // Insert into ORDERDETAIL table
    $insertOrderDetailSql = "INSERT INTO ORDERDETAIL (CUSTOMER_ID, SLOT_ID, ORDER_TOTAL) VALUES (:userId, :slotId, :orderTotal)";
    $insertOrderDetailStmt = oci_parse($connection, $insertOrderDetailSql);

    oci_bind_by_name($insertOrderDetailStmt, ':userId', $userId);
    oci_bind_by_name($insertOrderDetailStmt, ':slotId', $slotId);

    // Fetch and process fetched results to calculate order total
    while ($row = oci_fetch_assoc($stmt)) {
        $productId = $row['PRODUCT_ID'];
        $productPrice = $row['PRODUCT_PRICE'];
        $quantity = $row['QUANTITY'];
        $subtotal = $productPrice * $quantity;
        $orderTotal += $subtotal;
    }

    oci_bind_by_name($insertOrderDetailStmt, ':orderTotal', $orderTotal);

    // Execute the INSERT statement for ORDERDETAIL
    if (oci_execute($insertOrderDetailStmt)) {
        // Retrieve the generated ORDER_ID (assuming you have a sequence or trigger)
        $orderId = null; // Initialize orderId (change this based on your implementation)

        // Retrieve the last inserted ORDER_ID
        $sqlMaxOrderId = "SELECT MAX(order_id) AS max_id FROM ORDERDETAIL";
        $stmtMaxOrderId = oci_parse($connection, $sqlMaxOrderId);
        oci_execute($stmtMaxOrderId);
        $rowOrderId = oci_fetch_assoc($stmtMaxOrderId);
        $orderId = $rowOrderId['MAX_ID'];

        // Insert into ORDER_PRODUCT table
        $insertOrderProductSql = "INSERT INTO ORDER_PRODUCT (ORDER_ID, PRODUCT_ID, QUANTITY) VALUES (:orderId, :productId, :quantity)";
        $insertOrderProductStmt = oci_parse($connection, $insertOrderProductSql);

        oci_bind_by_name($insertOrderProductStmt, ':orderId', $orderId);
        oci_bind_by_name($insertOrderProductStmt, ':productId', $productId);
        oci_bind_by_name($insertOrderProductStmt, ':quantity', $quantity);

        // Re-execute the SQL statement to fetch cart items again
        oci_execute($stmt);

        // Fetch and process fetched results to insert into ORDER_PRODUCT
        while ($row = oci_fetch_assoc($stmt)) {
            $productId = $row['PRODUCT_ID'];
            $quantity = $row['QUANTITY'];

            // Bind variables for ORDER_PRODUCT insertion
            oci_bind_by_name($insertOrderProductStmt, ':productId', $productId);
            oci_bind_by_name($insertOrderProductStmt, ':quantity', $quantity);

            // Execute ORDER_PRODUCT insertion
            oci_execute($insertOrderProductStmt);
        }

        // Clear the user's cart (delete all cart items)
       // $clearCartSql = "DELETE FROM cart_product WHERE Cart_ID = :userId";
       // $clearCartStmt = oci_parse($connection, $clearCartSql);
        //oci_bind_by_name($clearCartStmt, ':userId', $userId);
       // oci_execute($clearCartStmt);
       sendEmailWithTotal($userId, $orderTotal);
               // Clear the user's cart (delete all cart items)
       $clearCartSql = "DELETE FROM cart_product WHERE Cart_ID = :userId";
       $clearCartStmt = oci_parse($connection, $clearCartSql);
       oci_bind_by_name($clearCartStmt, ':userId', $userId);
       oci_execute($clearCartStmt);
       //Display success message or redirect to confirmation page
        header("Location: orderconfirm.php?orderTotal=" . urlencode($orderTotal));
    } else {
        // Handle INSERT error
        echo "Error placing order. Please try again.";
    }
} else {
    // Invalid POST request or missing data
    echo "Invalid request. Please try again la.";
}




// Function to send email with order total
function sendEmailWithTotal($userId, $orderTotal) {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings for SMTP (using Gmail in this example)
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // SMTP server
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'sigdeldiwon@gmail.com';              // SMTP username
            $mail->Password   = 'xwms qyjh zqix rayt';                  // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to
    

        // Fetch email of the current logged-in user from the Customer table
        $email = getEmailForUser($userId);

        if ($email) {
            // Email parameters
            $mail->setFrom('Locallyclecl@gmail.com', 'LocallyCleck');
            $mail->addAddress($email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation';
            $mail->Body = "Thank you for your order! Your order has been placed with a total payment of $$orderTotal.";

            // Send the email
            $mail->send();
            echo 'Email sent successfully.';
        } else {
            echo 'Email address not found for the user.';
        }
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Function to fetch email from the Customer table based on user ID
function getEmailForUser($userId) {
    global $connection; // Access the global database connection object

    $stmt = oci_parse($connection, 'SELECT Email FROM Customer WHERE Customer_ID = :userId');
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);

    $result = oci_fetch_assoc($stmt);

    return $result['EMAIL'] ?? null; // Return email or null if not found
}
?>
