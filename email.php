<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send2FACodeToUser($email, $code) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 2;                      // Enable verbose debug output (0 = off)
        $mail->isSMTP();                           // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';      // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                  // Enable SMTP authentication
        $mail->Username   = ''; // Your Gmail address
        $mail->Password   = '';   // Your Gmail App Password (16 chars)
        $mail->SMTPSecure = 'tls';                 // Enable TLS encryption
        $mail->Port       = 587;                   // TCP port to connect to

        //Recipients
        $mail->setFrom('auth@nuemont-forums.edu', 'Neumont-Forums');
        $mail->addAddress($email);  // Add the recipient's email address

        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = 'Your 2FA Code from Neumont-Forums';
        $mail->Body    = "<b>Your 2FA code is: $code</b><br><p>This code is valid for the next 10 minutes.</p>";
        $mail->AltBody = "Your 2FA code is: $code\nThis code is valid for the next 10 minutes.";

        $mail->send();
        echo '✅ Message has been sent';
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
