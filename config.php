<?php
// config.php

$serverName = $_SERVER['SERVER_NAME'];

if ($serverName === 'localhost') {
    // Local environment
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = '---redacted--';
} else {
    // Production environment
    $db_host = '---redacted--';
    $db_user = '---redacted--';
    $db_pass = '---redacted--Y';
    $db_name = '---redacted--';
}

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
