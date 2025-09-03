<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "navbar.php";
include 'db_connect.php';

if (isset($_GET["code"], $_GET["email"])) {
    $code = $_GET["code"];
    $email = $_GET["email"];

    $response = verifyUser($email, $code);

    if (!$response['success']) {
        echo "<p style='color:red;'>❌ " . $response['message'] . "</p>";
    } else {
        $_SESSION["SignIN"] = true;
        $_SESSION['user_id'] = $response['user_id'];
        echo "<p style='color:green;'>✅ " . $response['message'] . "</p>";
        echo "<p>Welcome back, $email!</p>";
        header('Location: index.php');
        exit();
    }
}
?>
