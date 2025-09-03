<?php
session_start();
$is_logged_in = $_SESSION["SignIN"] ?? false;
?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">

<nav class="bg-black text-yellow-400 px-6 py-4 flex items-center justify-between shadow-lg">
    <!-- Left side: Logo and links -->
    <div class="flex items-center space-x-6">
        <img src="public/images/forum_Icon.png" alt="Neumont Forums Logo" class="h-6 w-6">
        <a href="index.php" class="hover:text-yellow-300 font-semibold">Home</a>
        <?php echo $is_logged_in ? 
            '<a href="yourposts.php" class="hover:text-yellow-300 font-semibold">Your Posts</a>' : 
            '<a href="sign_up.php" class="hover:text-yellow-300 font-semibold">Sign Up</a>'; 
        ?>
    </div>

    <!-- Right side: Profile button if logged in -->
    <?php if ($is_logged_in): ?>
        <a href="profile.php" class="bg-yellow-400 text-black px-4 py-1 rounded-md font-semibold hover:bg-yellow-300 transition">
            Profile
        </a>
    <?php endif; ?>
</nav>
