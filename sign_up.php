<?php
include "db_connect.php";
include "navbar.php"; 

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    addUser(
        $username,
        $email,
        $password
    );

    if (isset($_SESSION["signup_message"])) {
        $message = $_SESSION["signup_message"];
        unset($_SESSION["signup_message"]);
    }

}
?>

<div class="min-h-screen flex flex-col items-center justify-center bg-black text-yellow-400">
    <h1 class="text-4xl font-bold mb-6">Sign Up</h1>

    <?php if (!empty($message)): ?>
        <div class="mb-4 p-3 rounded-lg 
            <?php echo str_contains($message, '❌') ? 'bg-red-500 text-white' : 'bg-green-500 text-white'; ?>">
            <?php echo $message; ?>
            <?php if (str_contains($message, 'Email already registered')): ?>
                <a href="login.php" class="ml-4 underline hover:text-yellow-300">Go to Login</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="sign_up.php" method="POST" class="flex flex-col space-y-4 w-80">
        <input type="text" name="username" placeholder="Username" required class="px-4 py-2 rounded bg-gray-800 text-yellow-400 focus:outline-none"/>
        <input type="email" name="email" placeholder="Email" required class="px-4 py-2 rounded bg-gray-800 text-yellow-400 focus:outline-none"/>
        <input type="password" name="password" placeholder="Password" required class="px-4 py-2 rounded bg-gray-800 text-yellow-400 focus:outline-none"/>
        <button type="submit" class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded hover:bg-yellow-300 transition">
            Sign Up
        </button>
    </form>
</div>