<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['SignIN']) || !$_SESSION['SignIN']) {
    header("Location: sign_in.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['thread_id'], $_POST['content'])) {
    header("Location: index.php");
    exit();
}

$thread_id = (int)$_POST['thread_id'];
$content = trim($_POST['content']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id || $content === '') {
    // Invalid submission, redirect back to thread
    header("Location: thread.php?id=$thread_id");
    exit();
}

$post_id = createPost($thread_id, $user_id, $content);

if ($post_id) {
    header("Location: thread.php?id=$thread_id#post-$post_id");
    exit();
} else {
    header("Location: thread.php?id=$thread_id");
    exit();
}
