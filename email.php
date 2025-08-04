<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 2;                      // Enable verbose debug output (0 = off)
    $mail->isSMTP();                           // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';     // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                  // Enable SMTP authentication
    $mail->Username   = 'spacknat000@gmail.com'; // Your Gmail address
    $mail->Password   = 'efhi cvcl swnt tizm';  // Your Gmail App Password (16 chars)
    $mail->SMTPSecure = 'tls';                 // Enable TLS encryption
    $mail->Port       = 587;                   // TCP port to connect to

    //Recipients
    $mail->setFrom('auth@nuemont-forums.edu', 'Neumont-Fourms');
    $mail->addAddress('rooster0055@protonmail.com', 'person');  // Add a recipient

    // Content
    $mail->isHTML(true);                       // Set email format to HTML
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = '<b>Hello!</b> This is a test email sent using PHPMailer with Gmail SMTP.';
    $mail->AltBody = 'Hello! This is a test email sent using PHPMailer with Gmail SMTP.';

    $mail->send();
    echo '✅ Message has been sent';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
