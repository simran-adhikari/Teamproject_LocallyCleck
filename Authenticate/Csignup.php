<?php

//PHP MAILER STANDARD HEADERS
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer autoload file
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';



// Establish database connection
include "../connect.php";




if (isset($_FILES['image'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['gender'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $otp = mt_rand(1000, 9999);

    // Database connection assuming $connection is already established

    // Check if email already exists
    $sql_check_email = "SELECT COUNT(*) AS email_count FROM Customer WHERE email = :email";
    $stmt_check_email = oci_parse($connection, $sql_check_email);
    oci_bind_by_name($stmt_check_email, ':email', $email);
    oci_execute($stmt_check_email);
    $row = oci_fetch_assoc($stmt_check_email);
    $email_count = intval($row['EMAIL_COUNT']);
    oci_free_statement($stmt_check_email);

    if ($email_count > 0) {
        header("Location: Signuperror.html");
        exit;
    } else {
        // Prepare insert statement
        $sql_insert = "INSERT INTO Customer (first_name, last_name, email, password, gender, otp, profileImage)
                       VALUES (:first_name, :last_name, :email, :password, :gender, :otp, EMPTY_BLOB()) RETURNING profileImage INTO :image_data";

        $stmt_insert = oci_parse($connection, $sql_insert);

        // Bind parameters
        oci_bind_by_name($stmt_insert, ':first_name', $first_name);
        oci_bind_by_name($stmt_insert, ':last_name', $last_name);
        oci_bind_by_name($stmt_insert, ':email', $email);
        oci_bind_by_name($stmt_insert, ':password', $password);
        oci_bind_by_name($stmt_insert, ':gender', $gender);
        oci_bind_by_name($stmt_insert, ':otp', $otp);

        // Create a new LOB object for profileImage
        $imageBlob = oci_new_descriptor($connection, OCI_D_LOB);
        oci_bind_by_name($stmt_insert, ':image_data', $imageBlob, -1, OCI_B_BLOB);

    if (oci_execute($stmt_insert, OCI_DEFAULT)) {
        if ($imageBlob->save($imageData)) {
            oci_commit($connection);
            echo "Sucessfully Registered.";

        //Emailing Part

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();                     // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';// SMTP server
            $mail->SMTPAuth   = true;            // Enable SMTP authentication
            $mail->Username   = 'sigdeldiwon@gmail.com';// SMTP username
            $mail->Password   = 'xwms qyjh zqix rayt';   // SMTP password
            $mail->SMTPSecure = 'tls';           // Enable TLS encryption
            $mail->Port       = 587;             // TCP port to connect to
        
            // Recipients
            $mail->setFrom('sigdeldiwon@gmail.com', 'LocallyCleck');
            $mail->addAddress($email);           // Add a recipient
        
            // Content
            $mail->isHTML(true);                 // Set email format to HTML
            $mail->Subject = 'Confirm Your Email';
            $mail->Body = '
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
                        strong {
                            font-weight: bold;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Confirm Your Email</h1>
                        <p>Dear Customer,</p>
                        <p>Thank you for connecting with Locally Cleck! We appreciate your interest in our platform.</p>
                        <p>To confirm your email and complete the registration process, please use the following OTP:</p>
                        <p><strong>OTP:</strong> ' . $otp . '</p>
                        <p>We hope you have a great experience with Locally Cleck!</p>
                    </div>
                </body>
                </html>
            ';
        
            // Send email
            $mail->send();
            echo 'OTP sent to your email.';
        
            // Redirect to OTP verification page
            header("Location: Cotpverify.html");
            exit;
        } catch (Exception $e) {
            echo "OTP could not be sent. Error: {$mail->ErrorInfo}";
        }

















        } else {
            oci_rollback($connection);
            echo "Failed to save image data.";
        }
    } else {
        echo "Failed to execute SQL statement.";
    }

    oci_free_statement($stmt_insert);
    $imageBlob->free();
} 
}
else {
    echo "Error uploading image.";
    echo("/n");
    echo($_POST['first_name']);
    

}


oci_close($connection);
?>