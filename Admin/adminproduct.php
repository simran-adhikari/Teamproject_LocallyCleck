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
    <title>Approve Product</title>
    <link rel="icon" type="image/x-icon" href="../resource/logo.png" alt="Logo">
   
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
include("../connect.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['trader_id'])) {
    $trader_id = $_POST['trader_id'];
    $isverified = $_POST['isverified'];

    // Toggle the isVerified status
    $new_status = ($isverified == 'Y') ? 'N' : 'Y';

    $query = "UPDATE Product SET isverified = :new_status WHERE product_id = :trader_id";
    $stmt = oci_parse($connection, $query);
    oci_bind_by_name($stmt, ':new_status', $new_status);
    oci_bind_by_name($stmt, ':trader_id', $trader_id);

    $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

    if ($result) {
        echo "<div class='notification success'>Product status updated successfully!</div>";
    } else {
        $error_message = oci_error($stmt);
        echo "<div class='notification error'>Error updating trader status: " . $error_message['message'] . "</div>";
    }

    oci_free_statement($stmt);
}
?>
    <h1>Approve Product</h1>
    <table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Is Verified</th>
            <th>Action</th>
        </tr>
        <?php
        $query = "SELECT product_id, product_name, isverified FROM Product order by product_id";
        $stmt = oci_parse($connection, $query);
        oci_execute($stmt);

        while ($row = oci_fetch_assoc($stmt)) {
            $isVerifiedText = ($row['ISVERIFIED'] == 'Y') ? 'Approved' : 'Unapproved';
            echo "<tr>";
            echo "<td>" . $row['PRODUCT_ID'] . "</td>";
            echo "<td>" . $row['PRODUCT_NAME'] . "</td>";
            echo "<td>" . $isVerifiedText . "</td>";
            echo "<td><form action='' method='post'>
                    <input type='hidden' name='trader_id' value='" . $row['PRODUCT_ID'] . "'>
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