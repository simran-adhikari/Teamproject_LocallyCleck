<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/traderdashboardcss.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Trader Dashboard</title>
</head>
<style>
    body {
        background-color: #fbf6ee;
        font-family: 'Montserrat', sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        margin: 20px auto;
        width: 800px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        margin-left: 350px;
    }

    .profile-card {
        display: flex;
        justify-content: center; /* Center the content horizontally */
        align-items: center; /* Center the content vertically */
        height: 100%; /* Ensure the profile card takes full height of the container */
        
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center; /* Center text within the profile details */
        padding: 20px;
    }

    .profile-image {
        max-width: 200px; /* Set maximum width */
        max-height: auto; /* Set maximum height */
        width: auto; /* Maintain aspect ratio */
        height: auto; /* Maintain aspect ratio */
        border-radius: 50%;
        margin-bottom: 20px;
    }

    .profile-details {
        color: #333333;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .profile-details h2 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .profile-details p {
        font-size: 16px;
        margin: 0;
    }

    .trader {
        color: #ff9800;
        font-size: 40px;
        margin-top: 20px;
        text-align: center;
    }

    .social-icons a {
        text-decoration: none;
        margin: 0 10px;
        color: #333;
    }

    .social-icons a i {
        font-size: 24px;
    }
</style>
<body>
    <?php
    // Start the session
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['trader_id'])) {
        // Redirect to login page if user is not logged in
        header("Location: ../Home.php");
        exit();
    }

    // Database connection settings
    include("../connect.php");
    // Retrieve session user_id
    $session_user_id = $_SESSION['trader_id'];

    // Prepare SQL query to fetch shop and trader data
    $query = "SELECT s.bannerimage, s.profileimage, s.shop_name,s.description, t.name AS trader_name, t.email
              FROM shop s
              JOIN trader t ON s.trader_id = t.trader_id
              WHERE s.shop_id = :session_shop_id AND t.trader_id = :session_trader_id";

    // Prepare the SQL statement
    $statement = oci_parse($connection, $query);

    // Bind session user_id to both placeholders
    oci_bind_by_name($statement, ":session_shop_id", $session_user_id);
    oci_bind_by_name($statement, ":session_trader_id", $session_user_id);

    // Execute the prepared statement
    $result = oci_execute($statement);

    // Fetch the data
    $row = oci_fetch_assoc($statement);

    // Extract data from the row
    if ($row) {
        $bannerImage = base64_encode($row['BANNERIMAGE']->load());
        $profileImage = base64_encode($row['PROFILEIMAGE']->load());
        $shopName = htmlspecialchars($row['SHOP_NAME']);
        $description = htmlspecialchars($row['DESCRIPTION']);
        $traderName = htmlspecialchars($row['TRADER_NAME']);
        $temail=htmlspecialchars($row['EMAIL']);

        
    } else {
        echo 'No shop or trader found.';
    }

    // Free the statement and close the connection
    $_SESSION['display_name'] = $traderName;
    oci_free_statement($statement);
    oci_close($connection);
    ?>

    <?php
    include("nav/header.php");
    ?>


    <?php
    include("nav/sidenav.php");
    ?>

    <div class="main-content">
        <div class="container">
            <h2 class="trader">Trader Profile</h2>
            <div class="profile-card">
                <div class="profile-info">
                    <img src="data:image/jpeg;base64,<?php echo $profileImage; ?>" alt="Profile Image" class="profile-image">
                    <div class="profile-details">
                        <h2><?php echo $traderName; ?></h2>
                        <p><b>Shop Name: <?php echo $shopName; ?></b></p>
                        <?php echo  $description;?>
                        
                        <p>Contact Email: <?php echo $temail;?></p>
                        <p class="operating-hours">Operating Hours: Mon-Fri 
                            9:00 AM - 6:00 PM</p>
                            <p class="social-icons">
                            <a href="https://www.facebook.com"><i class="fab fa-facebook"></i></a>
                            <a href="https://www.twitter.com"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>