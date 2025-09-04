<?php
session_start();
require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$category_id = (int)$_GET['id'];

// Get category info
$stmt = $conn->prepare("SELECT * FROM Categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    header("Location: index.php");
    exit();
}

$threads = getThreadsByCategory($category_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($category['name']); ?> - Neumont Forums</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-black text-yellow-400 min-h-screen flex flex-col font-sans">

<?php include 'navbar.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow">
    <h1 class="text-4xl font-extrabold mb-8 text-yellow-400 drop-shadow-lg text-center">
        <?php echo htmlspecialchars($category['name']); ?>
    </h1>
    <a href="new_thread.php">New Thread</a>
    <?php if (empty($threads)): ?>
        <p class="text-center text-yellow-300 italic">No threads available in this category.</p>
    <?php else: ?>
        <ul class="max-w-3xl mx-auto space-y-6">
            <?php foreach ($threads as $thread): ?>
                <li class="bg-yellow-900 bg-opacity-20 rounded-lg p-6 shadow-md hover:bg-yellow-400 hover:text-black transition">
                    <a href="thread.php?id=<?php echo (int)$thread['thread_id']; ?>" class="block font-semibold text-xl mb-1">
                        <?php echo htmlspecialchars($thread['title']); ?>
                    </a>
                    <p class="text-yellow-300 text-sm mb-2">
                        Started by <span class="font-semibold"><?php echo htmlspecialchars($thread['username']); ?></span> on
                        <?php echo date('F j, Y', strtotime($thread['created_at'])); ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<footer class="bg-yellow-400 text-black py-4 text-center font-semibold">
    &copy; <?php echo date('Y'); ?> Neumont Forums
</footer>

</body>
</html>
