<?php

$connection = oci_connect("simran", "simran", "//localhost/xe");

if (!$connection) {
    $error_message = oci_error();
    echo "Failed to connect to Oracle: " . $error_message['message'];
    exit();
}

?>