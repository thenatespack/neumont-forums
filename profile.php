<?php
include "db_connect.php"; // your DB connection
include "navbar.php"; // optional, your menu

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch user info
$stmt = $conn->prepare("SELECT username, email, created_at FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "User not found.";
    exit;
}

$user = $result->fetch_assoc();
?>

<div class="min-h-screen flex flex-col items-center bg-black text-yellow-400 p-6">
    <h1 class="text-4xl font-bold mb-6">Your Profile</h1>

    <div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow">
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user["username"]); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
        <p><strong>Member since:</strong> <?php echo date("F j, Y", strtotime($user["created_at"])); ?></p>
    </div>
</div>
