<?php
$is_logged_in =false;
?>
<navbar>
    <a href="Home">Home</a>
    <a href="forum.php">Forum</a>
    <a href="Your Posts">Your Posts</a>
    <?php echo  $is_logged_in ?'': '<a href="Log In"></a>' ?>
</navbar>