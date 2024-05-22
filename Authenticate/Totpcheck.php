<?php
// Establish database connection
include "../connect.php";
// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch the entered OTP digits from the form
    $otp_digit1 = $_POST['digit1'];
    $otp_digit2 = $_POST['digit2'];
    $otp_digit3 = $_POST['digit3'];
    $otp_digit4 = $_POST['digit4'];

    // Concatenate the OTP digits to form the complete OTP
    $entered_otp = $otp_digit1 . $otp_digit2 . $otp_digit3 . $otp_digit4;

    // Prepare SQL statement to check if OTP exists in Customer table
    $sql = "SELECT * FROM Trader WHERE OTP = :entered_otp";
    $stmt = oci_parse($connection, $sql);
    oci_bind_by_name($stmt, ":entered_otp", $entered_otp);
    oci_execute($stmt);

    // Check if OTP exists in the database
    if ($row = oci_fetch_assoc($stmt)) {
        // OTP exists, update the isVerified column to 'Y'
        $update_sql = "UPDATE Trader SET otpverified = 'Y', OTP = 0  WHERE OTP = :entered_otp";
        $update_stmt = oci_parse($connection, $update_sql);
        oci_bind_by_name($update_stmt, ":entered_otp", $entered_otp);

        // Execute the update statement
        if (oci_execute($update_stmt)) {
            echo("Success");
            header("Location: Tradersignin.html");
            exit();
        } else {
            // Redirect back to OTP form with error message as URL parameter
            
            header("Location: Totpverify.html");
            echo("FAIL");
            exit();
        }
    } else {
        // Redirect back to OTP form with error message as URL parameter
        
        header("Location: Totpverify.html");
        echo("NO SUCH OTP");
        header("Location: Totpverify.html");

        exit();
    }

    // Free statement resources
    oci_free_statement($stmt);
    if (isset($update_stmt)) {
        oci_free_statement($update_stmt);
    }
}

// Close the database connection
oci_close($connection);
?>
