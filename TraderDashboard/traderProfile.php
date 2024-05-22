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
    <link rel="stylesheet" href="editProfile.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Edit Profile</title>
</head>
<body class="montserrat-text">

<?php
include("nav/header.php");
?>

    <!-- Dashboard Menu -->
<?php
include("nav/sidenav.php");
?>


 <?php
    include("../connect.php");
// Assume you have a session variable for the logged-in trader's ID
$trader_id = $_SESSION['trader_id'];
//$_SESSION['user_id'];

// Fetch trader and shop data based on trader_id
$query = "SELECT t.name AS trader_name, s.shop_id, s.shop_name, s.description, s.shop_type
          FROM trader t
          JOIN shop s ON t.trader_id = s.trader_id
          WHERE t.trader_id = :trader_id";

$statement = oci_parse($connection, $query);
oci_bind_by_name($statement, ":trader_id", $trader_id);
oci_execute($statement);

if ($row = oci_fetch_assoc($statement)) {
    $trader_name = $row['TRADER_NAME'];
    $shop_id = $row['SHOP_ID'];
    $shop_name = $row['SHOP_NAME'];
    $description = $row['DESCRIPTION'];
    $shop_type = $row['SHOP_TYPE'];
} else {
    // Handle case where no data is found (optional)
    // You might redirect the user or display an error message
    // For simplicity, we'll initialize variables with empty values
    $trader_name = '';
    $shop_id = '';
    $shop_name = '';
    $description = '';
    $shop_type = '';
}

// Close database connection (optional, usually closed automatically at the end of script execution)
oci_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
</head>
<body>
    <div class="content">
        <div class="edit-profile-container">
            <h2>Edit Profile</h2>
            <form action="php/update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="changeName">Change Name:</label>
                    <input type="text" id="changeName" name="changeName" value="<?php echo htmlspecialchars($trader_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="changeShopName">Change Shop Name:</label>
                    <input type="text" id="changeShopName" name="changeShopName" value="<?php echo htmlspecialchars($shop_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="changeShopImage">Change Shop Image:</label>
                    <input id="profile-image-upload" type="file" name="profile_image" required>
                </div>
                <div class="form-group">
                    <label for="changeShopBanner">Change Shop Banner:</label>
                    <input id="banner-image-upload" type="file" name="banner_image" required>
                </div>
                <div class="form-group">
                    <label for="changeDescription">Change Description (Shop):</label>
                    <textarea id="changeDescription" name="changeDescription" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="changeShopType">Change Shop Type:</label>
                    <select id="changeShopType" name="changeShopType" required>
                        <option value="Butcher" <?php if ($shop_type == 'Butcher') echo 'selected'; ?>>Butcher</option>
                        <option value="GreenGrocer" <?php if ($shop_type == 'GreenGrocer') echo 'selected'; ?>>GreenGrocer</option>
                        <option value="FishMonger" <?php if ($shop_type == 'FishMonger') echo 'selected'; ?>>FishMonger</option>
                        <option value="Bakery" <?php if ($shop_type == 'Bakery') echo 'selected'; ?>>Bakery</option>
                        <option value="Delicatessen" <?php if ($shop_type == 'Delicatessen') echo 'selected'; ?>>Delicatessen</option>
                    </select>
                </div>
                <input type="hidden" name="shop_id" value="<?php echo htmlspecialchars($shop_id); ?>">
                <button type="submit" class="edit-profile-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>

</body>
</html>
