<!DOCTYPE html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
include "navbar.php";
include 'db_connect.php';


if (isset($_GET["code"], $_GET["email"])) {
    $code = $_GET["code"];
    $email = $_GET["email"];

    $response = verifyUser($email, $code);

    if (!$response['success']) {
        echo "<p style='color:red;'>❌ " . $response['message'] . "</p>";
    } else {
        echo "<p style='color:green;'>✅ " . $response['message'] . "</p>";
        echo "<p>Welcome back, $email!</p>";
    }
}
?>
