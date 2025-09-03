<?php
$is_logged_in = false;
?>
<link rel="stylesheet" type="text/css" href="public\site.css">
<navbar class="nav-inner">
    <image src="public/images/forum_Icon.png" alt="Neumont Forums Logo" height="20px" width="20px"></image>
    <a href="index.php">Home</a>
    <a href="forum.php">Forum</a>
    <?php echo $is_logged_in ? '<a href="yourposts.php">Your Posts</a>' : '<a href="sign_up.php">Sign Up</a>' ?>
</navbar>