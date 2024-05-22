

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <title>Approve Traders</title>
    <style>
    .content {
    margin-left: 250px;
    padding: 20px;
    width: 80%;
    }
    table {
        max-width: 80%; /* Adjust the width as needed */
        margin: auto; /* Center the table */
        border-collapse: collapse;
        margin-bottom: 20px;
        margin-left: 30px;
    }
    th, td {
        padding: 8px; /* Reduce padding */
        text-align: center;
    }
    th {
        background-color: #f4f4f4;
    }
    .update-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 10px; /* Adjust button padding */
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
    }
    .notification {
        margin: 10px 0;
        padding: 10px;
        border-radius: 4px;
        width:500px;
    }
    .notification.success {
        background-color: #d4edda;
        color: #155724;
    }
    .notification.error {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
</head>

<body >
<?php
include("nav/header.php");
include("nav/sidenav.php");

?>

<div class="content">
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer Autoload file
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

include("../connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trader_id'])) {
    $trader_id = $_POST['trader_id'];
    $isverified = $_POST['isverified'];

    // Toggle the isVerified status
    $new_status = ($isverified == 'Y') ? 'N' : 'Y';

    $query = "UPDATE Trader SET isverified = :new_status WHERE trader_id = :trader_id";
    $stmt = oci_parse($connection, $query);
    oci_bind_by_name($stmt, ':new_status', $new_status);
    oci_bind_by_name($stmt, ':trader_id', $trader_id);

    $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    if ($result) {
        echo "<div class='notification success'>Trader status updated successfully!</div>";

        // Fetch trader's email
        $email_query = "SELECT email FROM Trader WHERE trader_id = :trader_id";
        $email_stmt = oci_parse($connection, $email_query);
        oci_bind_by_name($email_stmt, ':trader_id', $trader_id);
        oci_execute($email_stmt);
        $row = oci_fetch_assoc($email_stmt);
        $email = $row['EMAIL'];

        // Send email notification
        $subject = "Account Status Update";
        $message = ($new_status == 'Y') ? "Your Account Status is Live" : "Your Account Status is Not Updated";

        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->SMTPDebug = 0;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'sigdeldiwon@gmail.com';                     // SMTP username
            $mail->Password   = 'xwms qyjh zqix rayt';                         // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('sigdeldiwon@gmail.com', 'ADMIN');
            $mail->addAddress($email);                                 // Add a recipient

            // Content
            $mail->isHTML(true);                                      // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            echo "<div class='notification success'>Email notification sent successfully!</div>";
        } catch (Exception $e) {
            echo "<div class='notification error'>Error sending email: {$mail->ErrorInfo}</div>";
        }
    } else {
        $error_message = oci_error($stmt);
        echo "<div class='notification error'>Error updating trader status: " . $error_message['message'] . "</div>";
    }

    oci_free_statement($stmt);
}
?>

    <h1>Approve Trader</h1>
    <table>
        <tr>
            <th>Trader ID</th>
            <th>Trader Name</th>
            <th>Is Verified</th>
            <th>Action</th>
        </tr>
        <?php
        $query = "SELECT trader_id, name, isverified FROM Trader order by trader_id";
        $stmt = oci_parse($connection, $query);
        oci_execute($stmt);

        while ($row = oci_fetch_assoc($stmt)) {
            $isVerifiedText = ($row['ISVERIFIED'] == 'Y') ? 'Approved' : 'Unapproved';
            echo "<tr>";
            echo "<td>" . $row['TRADER_ID'] . "</td>";
            echo "<td>" . $row['NAME'] . "</td>";
            echo "<td>" . $isVerifiedText . "</td>";
            echo "<td><form action='' method='post'>
                    <input type='hidden' name='trader_id' value='" . $row['TRADER_ID'] . "'>
                    <input type='hidden' name='isverified' value='" . $row['ISVERIFIED'] . "'>
                    <input type='submit' class='update-button' value='Update'>
                  </form></td>";
            echo "</tr>";
        }
        oci_free_statement($stmt);
        oci_close($connection);
        ?>
    </table>
</div>
</body>
</html>