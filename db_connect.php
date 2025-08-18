<?php
include "email.php";
$servername = "localhost";
$dbname = "forum_db";
$username = "root";
$password = "";

// Enable detailed error reporting for easier debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->select_db($dbname)) {
    die("Database selection failed: " . $conn->error);
}

function addUser(string $username, string $email, string $password): void
{
    global $conn;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $currentTimestamp = date('Y-m-d H:i:s'); // Use PHP for current timestamp

    // Prepare and bind the statement to insert the user
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $currentTimestamp);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $user_id = $stmt->insert_id; // Get the user ID of the newly inserted user
    $stmt->close();

    // Generate a 6-digit 2FA code
    $twoFactorCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Random 6-digit code
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes')); // Set expiration time for the code (10 minutes)

    // Insert the 2FA code into the User_2fa table
    $stmt2fa = $conn->prepare("INSERT INTO User_2fa (user_id, code, expires_at) VALUES (?, ?, ?)");
    if ($stmt2fa === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt2fa->bind_param("iss", $user_id, $twoFactorCode, $expiresAt);

    if (!$stmt2fa->execute()) {
        die("2FA code insert failed: " . $stmt2fa->error);
    }

    $stmt2fa->close();

    // Send the 2FA code to the user (email/SMS, depending on your setup)
    send2FACodeToUser($email, $twoFactorCode); // Function to send the code (e.g., via email or SMS)

    echo "User added successfully with 2FA code.";
}


?>
