<?php
$servername = "localhost";
$dbname = "forum_db";
$username = "root";
$password = "";

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

    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP())");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    echo "User added successfully.";
}
?>
