<?php
$servername = "localhost";
$dbname = "forum_db";
$username = "root";
$password = "";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->select_db($dbname)) {
    die("Database selection failed: " . $conn->error);
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

        $mail->setFrom('auth@nuemont-forums.edu', 'Neumont-Forums');
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
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $currentTimestamp = date('Y-m-d H:i:s'); 
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $currentTimestamp);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $user_id = $stmt->insert_id; 
    $stmt->close();
    $twoFactorCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); 
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt2fa = $conn->prepare("INSERT INTO User_2fa (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt2fa->bind_param("iss", $user_id, $twoFactorCode, $expiresAt);
    if (!$stmt2fa->execute()) {
        die("2FA code insert failed: " . $stmt2fa->error);
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
        'message' => 'User successfully verified and authenticated.'
    ];
}




?>
