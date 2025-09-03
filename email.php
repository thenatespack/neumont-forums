<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send2FACodeToUser($email, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;                      
        $mail->isSMTP();                           
        $mail->Host       = 'smtp.gmail.com';      
        $mail->SMTPAuth   = true;                  
        $mail->Username   = ''; // Your Gmail address
        $mail->Password   = ''; // Your Gmail App Password (16 chars)
        $mail->SMTPSecure = 'tls';                 
        $mail->Port       = 587;                   
        $mail->setFrom('auth@nuemont-forums.edu', 'Neumont-Forums');
        $mail->addAddress($email); 
        $baseURL = 'http://127.0.0.1/neumont-forums/email.php'; 
        $encodedEmail = urlencode($email);
        $encodedCode = urlencode($code);
        $verifyURL = "$baseURL?email=$encodedEmail&code=$encodedCode";

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your 2FA Code from Neumont-Forums';
        $mail->Body    = "
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

include "navbar.php";

if(isset($_GET["code"])){
    $code = $_GET["code"];
    $email = $_GET["email"];
    if(verifyUser($email, $code)) {
        echo "log in";
    }
}


?>
