<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: Home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/cart.css" />
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Cart</title>
    <link rel="icon" type="image/x-icon" href="resource/logo.png" alt="Logo" />
    <style>
        .styled-button {
            background-color: #ff9800; /* Green background */
            border: none; /* Remove borders */
            color: white; /* White text */
            padding: 15px 32px; /* Some padding */
            text-align: center; /* Centered text */
            text-decoration: none; /* Remove underline */
            display: inline-block; /* Make the container of the link to fit the size of the button */
            font-size: 16px; /* Increase font size */
            margin: 4px 2px; /* Some margin */
            cursor: pointer; /* Pointer/hand icon */
            border-radius: 12px; /* Rounded corners */
            transition-duration: 0.4s; /* Transition effect */
        }

        .styled-button:hover {
            background-color: white; /* White background on hover */
            color: black; /* Black text on hover */
            border: 2px solid #4CAF50; /* Green border on hover */
        }
        .order-history-button {
            background-color: #28a745;
            color: white;
            border: 2px solid #218838;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-left: 20px;
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
<a href="customerorder.php" class="order-history-button">ORDER HISTORY</a>
<h1>YOUR CART</h1>
<?php
include "connect.php"; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle as needed
    header("Location: Authenticate/customersignin.html");
    exit;
}

// Get current user's cart ID (which is the user ID)
$userId = $_SESSION['user_id'];

// Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update quantity if "Update" button is pressed
    if (isset($_POST['update'])) {
        $productId = $_POST['product_id'];
        $newQuantity = intval($_POST['quantity']);

        // Update quantity in cart_product table
        $updateSql = "UPDATE cart_product SET Quantity = :newQuantity WHERE Cart_ID = :userId AND Product_ID = :productId";
        $updateStmt = oci_parse($connection, $updateSql);
        oci_bind_by_name($updateStmt, ':newQuantity', $newQuantity);
        oci_bind_by_name($updateStmt, ':userId', $userId);
        oci_bind_by_name($updateStmt, ':productId', $productId);
        oci_execute($updateStmt);
    }

    // Delete product if "Delete" button is pressed
    if (isset($_POST['delete'])) {
        $productId = $_POST['product_id'];

        // Delete product from cart_product table
        $deleteSql = "DELETE FROM cart_product WHERE Cart_ID = :userId AND Product_ID = :productId";
        $deleteStmt = oci_parse($connection, $deleteSql);
        oci_bind_by_name($deleteStmt, ':userId', $userId);
        oci_bind_by_name($deleteStmt, ':productId', $productId);
        oci_execute($deleteStmt);
    }
}

// SQL query to fetch cart items and corresponding product details
$sql = "SELECT p.Product_ID, p.Product_Name, p.Product_Price, p.Image, cp.Quantity, p.Quantity AS AvailableQuantity
        FROM product p
        JOIN cart_product cp ON p.Product_ID = cp.Product_ID
        WHERE p.isverified='Y'
        and p.quantity>2
        and cp.Cart_ID = :userId";

// Prepare the SQL statement
$stmt = oci_parse($connection, $sql);
oci_bind_by_name($stmt, ':userId', $userId);

// Execute the SQL statement
oci_execute($stmt);

// Initialize a flag to check if the cart is empty
$cartIsEmpty = true;

// Fetch and display cart items
while ($row = oci_fetch_assoc($stmt)) {
    $cartIsEmpty = false; // Set flag to false if at least one item is found
    $productId = $row['PRODUCT_ID'];
    $productName = $row['PRODUCT_NAME'];
    $productPrice = $row['PRODUCT_PRICE'];
    $quantity = $row['QUANTITY'];
    $availableQuantity = $row['AVAILABLEQUANTITY'];
    $maxOrderQuantity = min(20, $availableQuantity);
    $imageData = $row['IMAGE']->load();

    // Encode the BLOB data as base64
    $encodedImageData = base64_encode($imageData);

    // Determine the image type based on the first few bytes of the image data
    $header = substr($imageData, 0, 4);
    $imageType = 'image/jpeg'; // default to JPEG
    if (strpos($header, 'FFD8') === 0) {
        $imageType = 'image/jpeg'; // JPEG
    } elseif (strpos($header, '89504E47') === 0) {
        $imageType = 'image/png'; // PNG
    }

    // Display each cart item
    echo "
    <div class='product-section'>
        <div class='cart-item'>
            <a href='productdetail.php?product=" . urlencode($productName) . "'>
                <img src='data:{$imageType};base64,{$encodedImageData}' alt='{$productName}' class='product-image'>
            </a>
            <div class='product-details'>
                <h3>{$productName}</h3>
                <p>Price: $ {$productPrice}</p>
                <form method='POST'>
                    <input type='hidden' name='product_id' value='{$productId}'>
                    <div class='quantity-container'>
                        <label for='quantity_{$productId}'>Quantity:</label>
                        <input type='number' id='quantity_{$productId}' name='quantity' value='{$quantity}' min='1' max='{$maxOrderQuantity}' class='quantity-input'>
                        <button type='submit' name='update' class='delete-btn'>Update</button>
                    </div> <!-- Close quantity-container -->
                </form>
            </div> <!-- Close product-details -->
            <div class='delete-container'>
                <form method='POST'>
                    <input type='hidden' name='product_id' value='{$productId}'>
                    <button type='submit' name='delete' class='delete-btn'>Delete</button>
                </form>
            </div> <!-- Close delete-container -->
        </div> <!-- Close cart-item -->
    </div>"; // Close product-section
}
if ($cartIsEmpty) {
    echo '<p>Your cart is empty.</p>';
} else {
    // Only display the order section if the cart is not empty
    echo "
    <h2 class='orderheader'>Place Order</h2>
    <div class='order-section'>
        <div class='order-box'>
            <form method='POST' action='OrderHandle/placeorder.php'>
                <input type='hidden' name='user_id' value='{$userId}'>
                <label for='collection-day'>Collection Day:</label>
                <select id='collection-day' name='collection-day'>";
    // Get the current day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
    $currentDayOfWeek = date('w');
    // Define available options based on the current day
    $availableOptions = [];
    switch ($currentDayOfWeek) {
        case 2: // Tuesday
            $availableOptions = [
                ['value' => '4', 'label' => 'Thursday 10-13'],
                ['value' => '5', 'label' => 'Thursday 13-16'],
                ['value' => '6', 'label' => 'Thursday 16-19'],
                ['value' => '7', 'label' => 'Friday 10-13'],
                ['value' => '8', 'label' => 'Friday 13-16'],
                ['value' => '9', 'label' => 'Friday 16-19']
            ];
            break;
        case 3: // Wednesday
            $availableOptions = [
                ['value' => '7', 'label' => 'Friday 10-13'],
                ['value' => '8', 'label' => 'Friday 13-16'],
                ['value' => '9', 'label' => 'Friday 16-19']
            ];
            break;
        case 4: // Thursday
            $availableOptions = [
                ['value' => '1', 'label' => 'Wednesday 10-13'],
                ['value' => '2', 'label' => 'Wednesday 13-16'],
                ['value' => '3', 'label' => 'Wednesday 16-19']
            ];
            break;
        case 5: // Friday
            $availableOptions = [
                ['value' => '1', 'label' => 'Wednesday 10-13'],
                ['value' => '2', 'label' => 'Wednesday 13-16'],
                ['value' => '3', 'label' => 'Wednesday 16-19'],
                ['value' => '4', 'label' => 'Thursday 10-13'],
                ['value' => '5', 'label' => 'Thursday 13-16'],
                ['value' => '6', 'label' => 'Thursday 16-19']
            ];
            break;
        default: // Other days (Saturday | Sunday | Monday)
            $availableOptions = [
                ['value' => '1', 'label' => 'Wednesday 10-13'],
                ['value' => '2', 'label' => 'Wednesday 13-16'],
                ['value' => '3', 'label' => 'Wednesday 16-19'],
                ['value' => '4', 'label' => 'Thursday 10-13'],
                ['value' => '5', 'label' => 'Thursday 13-16'],
                ['value' => '6', 'label' => 'Thursday 16-19'],
                ['value' => '7', 'label' => 'Friday 10-13'],
                ['value' => '8', 'label' => 'Friday 13-16'],
                ['value' => '9', 'label' => 'Friday 16-19']
            ];
            break;
    }

    // Display available collection day options
    foreach ($availableOptions as $option) {
        echo '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
    }
    echo '</select>';
    echo '<br><br>';
    
    echo '<label for="promo-code">Add Promo Code:</label>';
    echo '<input type="text" id="promo-code" name="promo-code">';
    echo '<button class="place-order-btn"  onclick="applyPromo(event)">Apply Promo</button>';
    echo '<br><br>';
    
    echo '<button type="submit" class="styled-button">Place Order</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
}
?>

</div>

<?php
include("footer.php");
?>

</body>
<script>
    function applyPromo(event) {
        // Prevent form submission
        event.preventDefault();

        var promoInput = document.getElementById('promo-code').value;

        // Check if promoInput is empty or contains default value 'xyz'
        if (promoInput.trim() === '' || promoInput.trim().toLowerCase() === 'xyz') {
            applyDefaultPromo();
        } else if (promoInput.trim().toUpperCase() === 'PROMO25') {
            applyPromo25();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Promo Code',
                text: 'Please enter a valid promo code.',
            });
        }
    }

    function applyDefaultPromo() {
        // Set default promo code 'xyz'
        Swal.fire({
            icon: 'warning',
            title: 'No Promo Code',
            text: 'No Promo Code To Apply!',
        });
    }

    function applyPromo25() {
        // Apply promo code 'PROMO25'
        Swal.fire({
            icon: 'success',
            title: 'Promo Code Applied',
            text: 'Applying promo code: PROMO25',
        });
    }
</script>
</html>