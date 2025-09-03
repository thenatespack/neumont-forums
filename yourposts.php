<?php
include "db_connect.php";
include "navbar.php";

if (!isset($_SESSION["user_id"])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];$stmt = $conn->prepare("SELECT post_id, content, created_at FROM Posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="min-h-screen flex flex-col items-center bg-black text-yellow-400 p-6">
    <h1 class="text-4xl font-bold mb-6">Your Posts</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="card mb-4 p-4 w-full max-w-xl bg-gray-800 rounded-lg shadow">
                <p class="mb-2"><?php echo htmlspecialchars($post["content"]); ?></p>
                <small class="text-gray-400">Posted on <?php echo date("F j, Y, g:i a", strtotime($post["created_at"])); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-400">You haven’t posted anything yet.</p>
    <?php endif; ?>
</div>