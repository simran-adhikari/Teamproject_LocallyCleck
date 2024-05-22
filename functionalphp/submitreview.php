<?php
session_start();

if (isset($_SESSION['logged_in'])) {
    $customer_id = $_SESSION['user_id'];
    
    if (isset($_POST['userReview']) && isset($_POST['productId'])) {
        $review_comment = $_POST['userReview'];
        $product_id = $_POST['productId'];

        include("../connect.php");

        $insertQuery = "INSERT INTO REVIEW (CUSTOMER_ID, PRODUCT_ID, REVIEW_COMMENT)
                        VALUES (:customer_id, :product_id, :review_comment)";

        $stmt = oci_parse($connection, $insertQuery);
        oci_bind_by_name($stmt, ":customer_id", $customer_id);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":review_comment", $review_comment);

        $result = oci_execute($stmt);

        if ($result) {
            echo "Review submitted successfully!";
            // Redirect to a success page or display a success message
        } else {
            echo "Error submitting review.";
            // Handle error scenario
        }

        oci_close($connection);
    } else {
        echo "Invalid review data.";
        // Handle invalid data scenario
    }
} else {
    echo "User not logged in.";
    // Handle user not logged in scenario
}

?>