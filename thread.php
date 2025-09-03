<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$thread_id = (int)$_GET['id'];

// Get thread info + category info
$stmt = $conn->prepare("
    SELECT t.*, u.username, c.name AS category_name, c.category_id 
    FROM Threads t
    JOIN Users u ON t.user_id = u.user_id
    JOIN Categories c ON t.category_id = c.category_id
    WHERE t.thread_id = ?
");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
$thread = $result->fetch_assoc();
$stmt->close();

if (!$thread) {
    header("Location: index.php");
    exit();
}

$posts = getPostsByThread($thread_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($thread['title']); ?> - Neumont Forums</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-black text-yellow-400 min-h-screen flex flex-col font-sans">

<?php include 'navbar.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow max-w-4xl">
    <nav class="mb-6 text-yellow-300 text-sm">
        <a href="index.php" class="hover:text-yellow-500">Home</a> &raquo;
        <a href="category.php?id=<?php echo (int)$thread['category_id']; ?>" class="hover:text-yellow-500">
            <?php echo htmlspecialchars($thread['category_name']); ?>
        </a> &raquo;
        <span><?php echo htmlspecialchars($thread['title']); ?></span>
    </nav>

    <h1 class="text-4xl font-extrabold mb-8 text-yellow-400 drop-shadow-lg text-center">
        <?php echo htmlspecialchars($thread['title']); ?>
    </h1>

    <?php if (empty($posts)): ?>
        <p class="text-yellow-300 italic text-center">No posts in this thread yet.</p>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($posts as $post): ?>
                <div class="bg-yellow-900 bg-opacity-20 p-6 rounded-lg shadow-md hover:bg-yellow-400 hover:text-black transition">
                    <div class="mb-2 text-yellow-300 text-sm flex justify-between">
                        <span>Posted by <strong><?php echo htmlspecialchars($post['username']); ?></strong></span>
                        <span><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span>
                    </div>
                    <p class="whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['SignIN']) && $_SESSION['SignIN']): ?>
        <form action="post_create.php" method="post" class="mt-10 max-w-2xl mx-auto">
            <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
            <textarea name="content" rows="5" placeholder="Write your reply here..." required
                class="w-full p-4 rounded-md bg-yellow-900 bg-opacity-20 text-yellow-400 placeholder-yellow-600 border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
            <button type="submit"
                class="mt-3 px-6 py-2 bg-yellow-400 text-black rounded-md font-semibold hover:bg-yellow-300 transition">
                Post Reply
            </button>
        </form>
    <?php else: ?>
        <p class="text-center mt-10 text-yellow-300 italic">You must <a href="sign_in.php" class="underline hover:text-yellow-500">sign in</a> to reply.</p>
    <?php endif; ?>
</main>

<footer class="bg-yellow-400 text-black py-4 text-center font-semibold">
    &copy; <?php echo date('Y'); ?> Neumont Forums
</footer>

</body>
</html>
