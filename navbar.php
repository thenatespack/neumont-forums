<?php
$is_logged_in = false;
?>
<link rel="stylesheet" type="text/css" href="public\site.css">
<navbar class="nav-inner">
    <image src="public/images/forum_icon.png" alt="Neumont Forums Logo" height="20px" width="20px"></image>
    <a href="index.php">Home</a>
    <a href="forum.php">Forum</a>
    <a href="Your Posts">Your Posts</a>
    <?php echo $is_logged_in ? '' : '<a href="Log In"></a>' ?>
</navbar>