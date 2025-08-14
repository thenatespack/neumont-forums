<?php
include "db_connect.php";

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    addUser(
        $username,
        $email,
        $password
    );

}
?>


<form action="sign_up.php" method="POST">
    <input type="text" name="username"placeholder="username"></input>
    <input type="email" name="email" placeholder="email">
    <input type="password" name="password" placeholder="password"></input>
    <input type="submit" ></input>
</form>