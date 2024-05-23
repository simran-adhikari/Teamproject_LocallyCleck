<?php

// Include the PHPMailer autoload file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Establish database connection
include "../connect.php";

// Check if form data and image uploads are present
if (isset($_FILES['profile_image'], $_FILES['banner_image'], $_POST['trader_name'])) {
    $trader_name = $_POST['trader_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $shop_name = $_POST['shop_name'];
    $shop_type = $_POST['shop_type'];
    $description = $_POST['description'];
    $otp = mt_rand(1000, 9999);

    // Retrieve profile image data and name
    $profileImageData = file_get_contents($_FILES['profile_image']['tmp_name']);
    $profileImageName = $_FILES['profile_image']['name'];

    // Retrieve banner image data and name
    $bannerImageData = file_get_contents($_FILES['banner_image']['tmp_name']);
    $bannerImageName = $_FILES['banner_image']['name'];

    // Insert into Trader table
    $sqlTrader = "INSERT INTO Trader (name, email, password,otp)
                  VALUES (:trader_name, :email, :password,:otp)";
    $stmtTrader = oci_parse($connection, $sqlTrader);
    oci_bind_by_name($stmtTrader, ':trader_name', $trader_name);
    oci_bind_by_name($stmtTrader, ':email', $email);
    oci_bind_by_name($stmtTrader, ':password', $password);
    oci_bind_by_name($stmtTrader, ':otp', $otp);


    $sqlMaxId = "SELECT MAX(trader_id) AS max_id FROM Trader";
    $stmtMaxId = oci_parse($connection, $sqlMaxId);
    oci_execute($stmtMaxId);

    $row = oci_fetch_assoc($stmtMaxId);
    $trader_id = $row['MAX_ID'];
    $trader_id=$trader_id+1;
    
    // Insert into Shop table
    $sqlShop = "INSERT INTO Shop (shop_name, shop_type, description,trader_id,profileImage, bannerImage)
                VALUES (:shop_name, :shop_type, :description,:trader_id,EMPTY_BLOB(), EMPTY_BLOB()) RETURNING profileImage, bannerImage INTO :profile_image_data, :banner_image_data";
    $stmtShop = oci_parse($connection, $sqlShop);
    oci_bind_by_name($stmtShop, ':shop_name', $shop_name);
    oci_bind_by_name($stmtShop, ':shop_type', $shop_type);
    oci_bind_by_name($stmtShop, ':description', $description);
    oci_bind_by_name($stmtShop, ':trader_id', $trader_id);
    //oci_bind_by_name($stmtShop, ':gender', $gender);
    //oci_bind_by_name($stmtShop, ':otp', $otp);

    // Create new LOB objects for profileImage and bannerImage in Shop table
    $profileImageBlob = oci_new_descriptor($connection, OCI_D_LOB);
    $bannerImageBlob = oci_new_descriptor($connection, OCI_D_LOB);
    oci_bind_by_name($stmtShop, ':profile_image_data', $profileImageBlob, -1, OCI_B_BLOB);
    oci_bind_by_name($stmtShop, ':banner_image_data', $bannerImageBlob, -1, OCI_B_BLOB);

    // Execute Trader table insert
    if (oci_execute($stmtTrader, OCI_DEFAULT)) {
        // Execute Shop table insert
        if (oci_execute($stmtShop, OCI_DEFAULT)) {
            // Save images to LOB fields
            if ($profileImageBlob->save($profileImageData) && $bannerImageBlob->save($bannerImageData)) {
                oci_commit($connection);
                echo "Successfully Registered as Trader and Shop.";
                // Send email with OTP
                sendEmail($email, $otp);
            } else {
                oci_rollback($connection);
                echo "Failed to save image data.";
            }
        } else {
            oci_rollback($connection);
            echo "Failed to insert into Shop table.";
        }
    } else {
        oci_rollback($connection);
        echo "Failed to insert into Trader table.";
    }

    oci_free_statement($stmtTrader);
    oci_free_statement($stmtShop);
    $profileImageBlob->free();
    $bannerImageBlob->free();
} else {
    echo "Error uploading images or incomplete form data.";
}

oci_close($connection);

// Function to send email with OTP
function sendEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // SMTP server
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'simranadhikari89@gmail.com';                // SMTP username
        $mail->Password   = 'qjpa qbot ndqm ypdz';                  // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('sigdeldiwon@gmail.com', 'LocallyCleck');
        $mail->addAddress($email);    

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Trader Registration';
        
        // HTML content for the email
        $emailBody = '
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        font-size: 24px;
                        color: #333;
                    }
                    p {
                        font-size: 16px;
                        line-height: 1.6;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Trader Registration</h1>
                    <p>Thank you for connecting as a vendor to Locally Cleck!</p>
                    <p>To verify your email please use this OTP.</p>
                    <p><strong>OTP:</strong> ' . $otp . '</p>
                    <p>You can login to your account after we register your account.</p>
                    <p>Apologies for the wait, the registration process will take 8-10 hours.</p>
                </div>
            </body>
            </html>
        ';

        $mail->Body = $emailBody;

        // Send the email
        $mail->send();
        header("Location: Totpverify.html");
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
