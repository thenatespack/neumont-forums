<?php
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category_id = (int)$_POST['category_id'];
    $user_id = (int)$_POST['user_id'];
    $title = trim($_POST['title']);

    if (empty($title)) {
        die("Thread title is required.");
    }

    $new_thread_id = createThread($category_id, $user_id, $title);

    echo "Thread created successfully! Thread ID: " . $new_thread_id;
    header("Location: thread.php?id=" . $new_thread_id);
} else {
    echo "Invalid request.";
}

$conn->close();
?>
