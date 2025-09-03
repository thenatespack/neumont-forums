<?php
include "navbar.php";
include "db_connect.php";

$message = "";

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT user_id, password_hash FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedHash = $row["password_hash"];

        if (password_verify($password, $storedHash)) {
            $_SESSION["SignIN"] = true;   
            $_SESSION["user_id"] = $row["user_id"];
            $message = "✅ Login successful!";
            header("Location: index.php"); // redirect to dashboard or feed
            exit;
        } else {
            // ❌ Wrong password
           $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ Username not found.";
    }

    // echo $username;
    // $hash = '$2y$10$lLThaSBDcP/3o7sr3Tcn0.4hmyp9UVu5PomqwZKpJo8TaAUoNk3yi';
    // echo password_hash($password, PASSWORD_DEFAULT);
    // if (password_verify($password, $hash)) {
    //     echo '<br> true';
    //     $message = "✅ Login successful! Redirecting...";
    //     header("refresh:2; url=home.php");
    //     exit();
    // } else {
    //     $message = "❌ Invalid password.";
    // }

}
?>


<div class="min-h-screen flex flex-col items-center justify-center bg-black text-yellow-400">
    <h1 class="text-4xl font-bold mb-6">Login</h1>

    <?php if (!empty($message)): ?>
        <div class="mb-4 p-3 rounded-lg 
            <?php echo str_contains($message, '❌') ? 'bg-red-500 text-white' : 'bg-green-500 text-white'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="flex flex-col space-y-4 w-80">
        <input type="text" name="username" placeholder="Username" required
            class="px-4 py-2 rounded bg-gray-800 text-yellow-400 focus:outline-none"/>
        <input type="password" name="password" placeholder="Password" required
            class="px-4 py-2 rounded bg-gray-800 text-yellow-400 focus:outline-none"/>
        <button type="submit" 
            class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded hover:bg-yellow-300 transition">
            Login
        </button>
    </form>
</div>