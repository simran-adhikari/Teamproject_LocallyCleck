<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body></body>


<?php
session_start(); // Start the session if not already started

// Check if the user is submitting the order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['collection-day'])) {
    // Retrieve form data

    $userId = $_POST['user_id'];
    $slotId = $_POST['collection-day'];
    $promoCode=$_POST['promo-code'];



    // Calculate order total from cart items
    $totalQuantity = calculateOrderQuantity($userId);
   

    // Check if total quantity exceeds 20
    if ($totalQuantity > 0) {
        if ($totalQuantity > 20) {
            $_SESSION['error_message'] = "Total quantity exceeds 20. Please reduce the quantity.";
            header('Location: OrderError.php');
            exit;
        } else {
            $orderTotal= $_SESSION['ordertotal'];
            $discountedTotal = applyPromoCode($orderTotal, $promoCode);
            $_SESSION['discountedtotal']=$discountedTotal;


            echo '<script type="text/javascript">';
            echo 'Swal.fire({';
            echo '  title: "Confirm Payment",';
            echo '  text: "Your total order amount is: £' . $discountedTotal . '. Do you want to proceed with the payment?",';
            echo '  icon: "question",';
            echo '  showCancelButton: true,';
            echo '  confirmButtonText: "Yes",';
            echo '  cancelButtonText: "No"';
            echo '}).then((result) => {';
            echo '  if (result.isConfirmed) {';
            echo '    window.location.href = "successpaypal.php?amount=' . $discountedTotal . '&user_id=' . $userId . '&slot_id=' . $slotId . '";';
            echo '  } else {';
            echo '    window.location.href = "../cart.php";';
            echo '  }';
            echo '});';
            echo '</script>';
        }
    } else {
        $_SESSION['error_message'] = "Cart Is Empty!";
        header('Location: OrderError.php');
        exit;
    }
} else {
    // Invalid POST request or missing data
    $_SESSION['error_message'] = "Invalid Request Please Try Ordering Again!";
    header('Location: OrderError.php');
    exit;
}

// Function to calculate order total from cart items
function calculateOrderQuantity($userId) {
    include "../connect.php"; // Database connection

    // Initialize order total and quantity
    $orderTotal = 0;
    $totalQuantity = 0;

    // SQL query to fetch cart items, corresponding product details, and offer details
    $sql = "SELECT p.Product_Price, cp.Quantity, o.Offer_Percentage
            FROM product p
            JOIN cart_product cp ON p.Product_ID = cp.Product_ID
            LEFT JOIN offer o ON p.Offer_ID = o.Offer_ID
            WHERE cp.Cart_ID = :userId";

    // Prepare and execute the SQL statement
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);

    // Calculate order total based on fetched cart items and offers
    while ($row = oci_fetch_assoc($stmt)) {
        $productPrice = $row['PRODUCT_PRICE'];
        $quantity = $row['QUANTITY'];
        $offerPercentage = isset($row['OFFER_PERCENTAGE']) ? $row['OFFER_PERCENTAGE'] : 0;

        // Calculate discounted price if an offer is available
        $discountedPrice = $productPrice * ((100 - $offerPercentage) / 100);

        // Calculate subtotal for the given quantity of the product
        $subtotal = $discountedPrice * $quantity;

        // Accumulate the subtotal into the total order price
        $orderTotal += $subtotal;

        // Accumulate the quantity into the total quantity
        $totalQuantity += $quantity;
    }

    // Store total quantity and order total in session for reference if needed
    $_SESSION['total_quantity'] = $totalQuantity;
    $_SESSION['ordertotal'] = $orderTotal;

    // Return the total quantity
    return $totalQuantity;
}

// Function to initiate PayPal payment and redirect user
function initiatePayPalPayment($orderTotal, $userId, $slotId) {
    // Check if total quantity exceeds 20
    if ($_SESSION['total_quantity'] > 20) {
        $_SESSION['error_message'] = "Order Items Exceeds 20 count!";
        header('Location: OrderError.php');
        exit;
    }

    // PayPal sandbox credentials
    $paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    $paypalEmail = 'sb-zmbun30558672@business.example.com'; // Your PayPal sandbox seller email
    $returnUrl = 'http://localhost/cleck/OrderHandle/successpaypal.php'; // URL to redirect after payment success
    $cancelUrl = 'http://localhost/cleck/OrderHandle/OrderError.php'; // URL to redirect if payment is canceled

    // Build PayPal payment URL
    $paypalUrl .= '?cmd=_xclick';
    $paypalUrl .= '&business=' . urlencode($paypalEmail);
    $paypalUrl .= '&item_name=' . urlencode('Order Payment');
    $paypalUrl .= '&amount=' . urlencode($orderTotal);
    $paypalUrl .= '&currency_code=GBP';
    $paypalUrl .= '&return=' . urlencode($returnUrl);
    $paypalUrl .= '&cancel_return=' . urlencode($cancelUrl);

    // Save order details in session to use after PayPal payment
    $_SESSION['collection_id'] = $slotId;

    // Redirect user to PayPal for payment
    header('Location: ' . $paypalUrl);
    exit;
}

function applyPromoCode($orderTotal, $promoCode)
{
    // Check if a valid promo code is entered (e.g., "PROMO25")
    if ($promoCode === 'PROMO25') {
        // Apply a discount (e.g., 25% off)
        $discountPercentage = 0.25; // 25% discount
        $discountedTotal = $orderTotal - ($orderTotal * $discountPercentage);
        return $discountedTotal;
    } else {
        // No valid promo code applied, return original total
        return $orderTotal;
    }
}