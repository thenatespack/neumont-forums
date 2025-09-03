<?php
include "email.php";
$servername = "localhost";
$dbname = "forum_db";
$username = "root";
$password = "";

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
    $currentTimestamp = date('Y-m-d H:i:s'); 
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $currentTimestamp);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $user_id = $stmt->insert_id; 
    $stmt->close();
    $twoFactorCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); 
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt2fa = $conn->prepare("INSERT INTO User_2fa (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt2fa->bind_param("iss", $user_id, $twoFactorCode, $expiresAt);
    if (!$stmt2fa->execute()) {
        die("2FA code insert failed: " . $stmt2fa->error);
    }
    $stmt2fa->close();
    send2FACodeToUser($email, $twoFactorCode);
}

function verifyUser($email, $twoFactorCode): bool {
    global $conn;

    // Step 1: Get the user ID from the email
    $stmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        return false; // No user found
    }

    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $stmt->close();

    // Step 2: Check the 2FA code
    $stmt = $conn->prepare("SELECT * FROM User_2fa WHERE user_id = ? AND code = ? AND expires_at >= NOW()");
    $stmt->bind_param("is", $user_id, $twoFactorCode);
    $stmt->execute();
    $result = $stmt->get_result();

    $isValid = $result->num_rows > 0;

    $stmt->close();

    return $isValid;
}



?>
