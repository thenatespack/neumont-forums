<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$servername = "localhost";
$dbname = "forum_db";
$username = "root";
$password = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function createCategory(string $name, string $description = null): int
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();
    $category_id = $stmt->insert_id;
    $stmt->close();
    return $category_id;
}

function getCategories(): array
{
    global $conn;
    $result = $conn->query("SELECT * FROM Categories ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createThread(int $category_id, int $user_id, string $title): int
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Threads (category_id, user_id, title) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $category_id, $user_id, $title);
    $stmt->execute();
    $thread_id = $stmt->insert_id;
    $stmt->close();
    return $thread_id;
}

function getThreadsByCategory(int $category_id): array
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT t.*, u.username 
        FROM Threads t 
        JOIN Users u ON t.user_id = u.user_id 
        WHERE t.category_id = ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $threads = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $threads;
}

function createPost(int $thread_id, int $user_id, string $content): int
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO Posts (thread_id, user_id, content) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iis", $thread_id, $user_id, $content);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $post_id = $stmt->insert_id;
    $stmt->close();
    return $post_id;
}


function getPostsByThread(int $thread_id): array
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.*, u.username 
        FROM Posts p 
        JOIN Users u ON p.user_id = u.user_id 
        WHERE p.thread_id = ? 
        ORDER BY p.created_at ASC
    ");
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $posts;
}

function assignRoleToUser(int $user_id, int $role_id): bool
{
    global $conn;
    $stmt = $conn->prepare("INSERT IGNORE INTO UserRoles (user_id, role_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $role_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getUserRoles(int $user_id): array
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.* 
        FROM Roles r 
        JOIN UserRoles ur ON r.role_id = ur.role_id 
        WHERE ur.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $roles = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $roles;
}

function getUserById(int $user_id): ?array
{
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, username, email, UserAuthed, created_at FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user ?: null;
}

// --- New functions ---

// Edit a post (only content can be changed)
function editPost(int $post_id, int $user_id, string $newContent): bool
{
    global $conn;

    // Ensure the user owns the post before editing
    $stmt = $conn->prepare("SELECT user_id FROM Posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if (!$post || $post['user_id'] !== $user_id) {
        return false; // Unauthorized or no such post
    }

    $stmt = $conn->prepare("UPDATE Posts SET content = ? WHERE post_id = ?");
    $stmt->bind_param("si", $newContent, $post_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function deletePost(int $post_id, int $user_id): bool
{
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM Posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    if (!$post || $post['user_id'] !== $user_id) {
        return false; 
    }

    $stmt = $conn->prepare("DELETE FROM Posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function deleteThread(int $thread_id, int $user_id): bool
{
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM Threads WHERE thread_id = ?");
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $thread = $result->fetch_assoc();
    $stmt->close();

    if (!$thread || $thread['user_id'] !== $user_id) {
        return false;
    }

    $stmt = $conn->prepare("DELETE FROM Threads WHERE thread_id = ?");
    $stmt->bind_param("i", $thread_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function deleteCategory(int $category_id): bool
{
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) AS thread_count FROM Threads WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['thread_count'];
    $stmt->close();

    if ($count > 0) {
        return false;
    }

    $stmt = $conn->prepare("DELETE FROM Categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}

function searchThreads(string $keyword): array
{
    global $conn;
    $likeKeyword = "%" . $keyword . "%";
    $stmt = $conn->prepare("
        SELECT t.*, u.username, c.name AS category_name 
        FROM Threads t 
        JOIN Users u ON t.user_id = u.user_id 
        JOIN Categories c ON t.category_id = c.category_id 
        WHERE t.title LIKE ? 
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("s", $likeKeyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $threads = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $threads;
}

function searchPosts(string $keyword): array
{
    global $conn;
    $likeKeyword = "%" . $keyword . "%";
    $stmt = $conn->prepare("
        SELECT p.*, u.username, t.title AS thread_title, c.name AS category_name 
        FROM Posts p 
        JOIN Users u ON p.user_id = u.user_id 
        JOIN Threads t ON p.thread_id = t.thread_id 
        JOIN Categories c ON t.category_id = c.category_id
        WHERE p.content LIKE ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("s", $likeKeyword);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $posts;
}

function send2FACodeToUser($email, $code)
{
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'spacknat000@gmail.com'; // Your Gmail address
        $mail->Password = ''; // Your Gmail App Password (use App Password, not your actual password)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('auth@neumont-forums.edu', 'Neumont-Forums');
        $mail->addAddress($email);

        $baseURL = 'http://127.0.0.1/neumont-forums/email.php';
        $encodedEmail = urlencode($email);
        $encodedCode = urlencode($code);
        $verifyURL = "$baseURL?email=$encodedEmail&code=$encodedCode";

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your 2FA Code from Neumont-Forums';
        $mail->Body = "
            <h3>Your 2FA code is: <b>$code</b></h3>
            <p>This code is valid for the next 10 minutes.</p>
            <p>You can verify your code by clicking the link below:</p>
            <p><a href=\"$verifyURL\">Verify 2FA Code</a></p>
        ";
        $mail->AltBody = "Your 2FA code is: $code\n\nThis code is valid for the next 10 minutes.\nVerify here: $verifyURL";

        $mail->send();
        echo '✅ Message has been sent';
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function addUser(string $username, string $email, string $password): void
{
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
        if ($result->num_rows > 0) {
        $stmt->close();
        $_SESSION["signup_message"] = "❌ Email already registered.";
        return;;
    }
    $stmt->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $currentTimestamp = date('Y-m-d H:i:s'); 
    $stmt = $conn->prepare(
        "INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $currentTimestamp);

    if (!$stmt->execute()) {
         $_SESSION["signup_message"] = "❌ Error: " . $stmt->error;
        return;
    }
    
    $user_id = $stmt->insert_id; 
    $stmt->close();
    $twoFactorCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); 
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt2fa = $conn->prepare("INSERT INTO User_2fa (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt2fa->bind_param("iss", $user_id, $twoFactorCode, $expiresAt);
    if (!$stmt2fa->execute()) {
        $_SESSION["signup_message"] = "2FA code insert failed: " . $stmt2fa->error;
        return;
    }
    $stmt2fa->close();
    send2FACodeToUser($email, $twoFactorCode);
}

function verifyUser($email, $twoFactorCode): array {
    global $conn;

    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        return [
            'success' => false,
            'reason' => 'email_not_found',
            'message' => 'No user found with that email.'
        ];
    }

    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM User_2fa WHERE user_id = ? AND code = ?");
    $stmt->bind_param("is", $user_id, $twoFactorCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();

        $stmt = $conn->prepare("SELECT * FROM User_2fa WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $allCodes = $stmt->get_result();

        $codeFound = false;
        $codeExpired = false;

        while ($row = $allCodes->fetch_assoc()) {
            if ($row['code'] === $twoFactorCode) {
                $codeFound = true;
                if (strtotime($row['expires_at']) < time()) {
                    $codeExpired = true;
                }
                break;
            }
        }

        $stmt->close();

        if ($codeFound && $codeExpired) {
            return [
                'success' => false,
                'reason' => 'code_expired',
                'message' => 'The two-factor code has expired.'
            ];
        } else {
            return [
                'success' => false,
                'reason' => 'invalid_code',
                'message' => 'Invalid two-factor authentication code.'
            ];
        }
    }

    $stmt->close();
    $stmt = $conn->prepare("UPDATE Users SET UserAuthed = TRUE WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        $stmt->close();
        return [
            'success' => false,
            'reason' => 'auth_update_failed',
            'message' => 'User verified, but failed to update auth status.'
        ];
    }

    $stmt->close();

    return [
        'success' => true,
        'reason' => 'verified',
        'message' => 'User successfully verified and authenticated.',
        'user_id' => $user_id  // <--- added here
    ];
}





?>
