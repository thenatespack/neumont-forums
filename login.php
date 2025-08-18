<?php
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    echo $username;
    $hash = '$2y$10$lLThaSBDcP/3o7sr3Tcn0.4hmyp9UVu5PomqwZKpJo8TaAUoNk3yi';
    echo password_hash($password, PASSWORD_DEFAULT);
    if (password_verify($password, $hash)) {
        echo '<br> true';
    }
}
?>


<form action="login.php" method="POST">
    <input type="text" name="username"></input>
    <input type="password" name="password"></input>
    <input type="submit"></input>
</form>