<?php
include "../connect.php"; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require('../fpdf/fpdf.php');


session_start(); // Start the session if not already started

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Retrieve user and slot IDs from session
    $userId = $_SESSION['user_id'];
    $slotId = $_SESSION['collection_id'];

    // Initialize orderTotal
    $orderTotal = 0;

    // SQL query to fetch cart items with product details and offer information
    $sql = "SELECT p.Product_ID, p.Product_Price, cp.Quantity, p.OFFER_ID, o.OFFER_PERCENTAGE
            FROM product p
            JOIN cart_product cp ON p.Product_ID = cp.Product_ID
            JOIN OFFER o ON p.OFFER_ID = o.OFFER_ID
            WHERE cp.Cart_ID = :userId";

    // Prepare the SQL statement
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':userId', $userId);

    // Execute the SQL statement
    oci_execute($stmt);

    // Calculate orderTotal with discounts applied
    while ($row = oci_fetch_assoc($stmt)) {
        $productPrice = $row['PRODUCT_PRICE'];
        $quantity = $row['QUANTITY'];
        $offerPercentage = isset($row['OFFER_PERCENTAGE']) ? $row['OFFER_PERCENTAGE'] : 0;

        // Calculate discounted price and subtotal for the current product
        $discountedPrice = $productPrice * (1 - ($offerPercentage / 100));
        $subtotal = $discountedPrice * $quantity;

        // Add discounted subtotal to orderTotal
        $orderTotal += $subtotal;

        // Update product quantity in the Product table
        $updateProductQuantitySql = "UPDATE product SET Quantity = Quantity - :quantity WHERE Product_ID = :productId";
        $updateProductQuantityStmt = oci_parse($connection, $updateProductQuantitySql);
        oci_bind_by_name($updateProductQuantityStmt, ':quantity', $quantity);
        oci_bind_by_name($updateProductQuantityStmt, ':productId', $row['PRODUCT_ID']);
        oci_execute($updateProductQuantityStmt);
    }

    // Insert order details into ORDERDETAIL table
    $insertOrderDetailSql = "INSERT INTO ORDERDETAIL (CUSTOMER_ID, SLOT_ID, ORDER_TOTAL) VALUES (:userId, :slotId, :orderTotal)";
    $insertOrderDetailStmt = oci_parse($connection, $insertOrderDetailSql);

    oci_bind_by_name($insertOrderDetailStmt, ':userId', $userId);
    oci_bind_by_name($insertOrderDetailStmt, ':slotId', $slotId);
    oci_bind_by_name($insertOrderDetailStmt, ':orderTotal',  $orderTotal);

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

        // Insert each product into ORDER_PRODUCT table
        $insertOrderProductSql = "INSERT INTO ORDER_PRODUCT (ORDER_ID, PRODUCT_ID, QUANTITY) VALUES (:orderId, :productId, :quantity)";
        $insertOrderProductStmt = oci_parse($connection, $insertOrderProductSql);

        // Re-execute the SQL statement to fetch cart items again
        oci_execute($stmt);

        // Insert each product into ORDER_PRODUCT table
        while ($row = oci_fetch_assoc($stmt)) {
            $productId = $row['PRODUCT_ID'];
            $quantity = $row['QUANTITY'];

            // Bind variables for ORDER_PRODUCT insertion
            oci_bind_by_name($insertOrderProductStmt, ':orderId', $orderId);
            oci_bind_by_name($insertOrderProductStmt, ':productId', $productId);
            oci_bind_by_name($insertOrderProductStmt, ':quantity', $quantity);

            // Execute ORDER_PRODUCT insertion
            oci_execute($insertOrderProductStmt);
        }

        // Clear the user's cart (delete all cart items)
        $clearCartSql = "DELETE FROM cart_product WHERE Cart_ID = :userId";
        $clearCartStmt = oci_parse($connection, $clearCartSql);
        oci_bind_by_name($clearCartStmt, ':userId', $userId);
        oci_execute($clearCartStmt);

        // Send email notification with total amount
        sendEmailWithTotal($userId,  $orderTotal,$orderId);

        // Redirect to confirmation page with order total
        header("Location: orderconfirm.php?orderTotal=" . urlencode( $orderTotal));
        exit; // Exit to prevent further output
    } else {
        // Handle INSERT error
        echo "Error placing order. Please try again.";
    }
} else {
    // Invalid session or missing user ID
    echo "Invalid request. Please try again.";
}



// Function to send email with order total



function generateInvoicePDF($orderTotal, $orderId, $userId) {

    include("../connect.php");

    // Fetch customer details
    $stmt = oci_parse($connection, 'SELECT FIRST_NAME, Email FROM Customer WHERE Customer_ID = :userId');
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);
    $cdata = oci_fetch_assoc($stmt);

    // Fetch product details
    $productStmt = oci_parse($connection, 
        'SELECT p.Product_Name, op.Quantity
        FROM Product p
        JOIN Order_Product op ON p.Product_ID = op.Product_ID
        WHERE op.Order_ID = :orderId');
    oci_bind_by_name($productStmt, ':orderId', $orderId);
    oci_execute($productStmt);

    // Create new PDF instance
    $pdf = new FPDF();

    // Add a page
    $pdf->AddPage();

    // Set font to Arial
    $pdf->SetFont('Arial', '', 12);

    // Add logo
    $pdf->Image('../resource/logo.png', 10, 10, 50);

    // Move the cursor down to avoid overlapping with the logo
    $pdf->Ln(40); // Adding line breaks to move the cursor 40 units down

    // Add order ID and customer details
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Order Invoice LOCALLY CLECK', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Order ID: ' . $orderId, 0, 1, 'L');
    $pdf->Cell(0, 10, 'Customer Name: ' . $cdata['FIRST_NAME'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Customer Email: ' . $cdata['EMAIL'], 0, 1, 'L');
    $pdf->Cell(0, 10, '', 0, 1); // Empty cell for spacing

    // Add table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(100, 10, 'Product Name', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Ln();

    // Add product details
    $pdf->SetFont('Arial', '', 12);
    while ($row = oci_fetch_assoc($productStmt)) {
        $pdf->Cell(100, 10, $row['PRODUCT_NAME'], 1);
        $pdf->Cell(30, 10, $row['QUANTITY'], 1);
        $pdf->Ln();
    }

    // Add total payment and thank you note
    $pdf->Cell(0, 10, '', 0, 1); // Empty cell for spacing
    $pdf->Cell(0, 10, 'Total Payment: GBP ' . $orderTotal, 0, 1, 'L');
    $pdf->Cell(0, 10, 'Thank you for your order!', 0, 1, 'L');

    // Output the PDF
    $pdf->Output('invoice.pdf', 'F');
}
function sendEmailWithTotal($userId, $orderTotal,$orderId) {
    // Generate invoice PDF
    generateInvoicePDF($orderTotal, $orderId, $userId);

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings for SMTP (using Gmail in this example)
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // SMTP server
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'sigdeldiwon@gmail.com';                // SMTP username
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
            $mail->Subject = 'Order Confirmation with Invoice';

            // HTML content for the email
            $emailBody = '
                <html>
                <body>
                    <div style="text-align: center;">
                        <h2>Thank you for your order!</h2>
                        <p>Your order has been placed with a total payment of £' . $orderTotal . '.</p>
                        <p>Please find the invoice attached.</p>
                    </div>
                </body>
                </html>
            ';

            $mail->Body = $emailBody;

            // Attach invoice PDF
            $pdfPath = 'invoice.pdf';
            $mail->addAttachment($pdfPath);

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
