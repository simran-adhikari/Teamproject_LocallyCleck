<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form fields and remove whitespace
    $name = trim($_POST["name"]);
    $surname = trim($_POST["surname"]);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST["phone"]);
    $message = trim($_POST["message"]);

    // Check if all fields are filled
    if ($name != "" && $surname != "" && $email != "" && $message != "") {
        // Set up the recipient email address
        $to = "simranadhikari89@gmail.com";

        // Set up the email subject
        $subject = "Query Contact Us Form";

        // Set up PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();                        // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';   // Set the SMTP server to send through
            $mail->SMTPAuth   = true;               // Enable SMTP authentication
            $mail->Username   = 'simranadhikari89@gmail.com';   // SMTP username
            $mail->Password   = 'qjpa qbot ndqm ypdz';    // SMTP password
            $mail->SMTPSecure = 'tls';              // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($to);                 // Add a recipient

            // Content
            $mail->isHTML(false);                   // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = "Name: $name\nSurname: $surname\nEmail: $email\nPhone: $phone\nMessage:\n$message";

            $mail->send();
            //echo "<p>Your message has been sent successfully. We will get back to you soon!</p>";
        } catch (Exception $e) {
            echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<p>Please fill in all the required fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    <title>Contact Us</title>
    <link rel="icon" type="image/x-icon" href="resource/logo.png" alt="Logo">

</head>
<body>

<?php
session_start();
include("header.php");
?>

<div class="container">

    <h1 class="brand"><span><b>Contact Us</b></Contact></span></h1>

    <div class="wrapper">

        <div class="company-info">
            <div class="logo">
                <img src="resource/logo.png" alt="logo" width="100" height="100"></a>
            </div>

            <h2>Locally Cleck</h2>


            <ul>
			<li><i class="fa fa-map-marker"></i> Cleckhuddersfax, U.K </li>
                <li><i class="fa fa-phone"></i>  01484 512222</li>
                <li><i class="fa fa-envelope"></i> locallycleck@gmail.com</li>
            </ul>
            <br>
            <h3>Visit our social media handles</h3>

            <p class="social-icons">
                <a href="https://www.facebook.com"><i class="fab fa-facebook"></i></a>
                <a href="https://www.twitter.com"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
            </p>
        </div>


        <!-- CONTACT FORM -->
        <div class="contact">
            <h3>Get in Touch with Us! &nbsp;Fill out the Form Below</h3>

            <form id="contact-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <p>
                    <label>First name</label>
                    <input type="text" name="name" id="name" required>
                </p>

                <p>
                    <label>Last name</label>
                    <input type="text" name="surname" id="surname" required>
                </p>

                <p>
                    <label>E-mail Address</label>
                    <input type="email" name="email" id="email" required>
                </p>

                <p>
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="phone">
                </p>

                <p class="full">
                    <label>Message</label>
                    <textarea name="message" rows="5" id="message" required></textarea>
                </p>

                <p class="full">
                    <button type="submit">Submit</button>
                </p>

            </form>

        </div>


    </div>

</div>

<?php

include("footer.php");
?>
</body>
</html>
