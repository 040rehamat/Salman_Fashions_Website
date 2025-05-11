<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/Exception.php';
require __DIR__ . '/PHPMailer.php';
require __DIR__ . '/SMTP.php';

function sendConfirmationEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
   
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rahemanabdul696@gmail.com'; // Your Gmail
        $mail->Password   = '@Raheman2002';   // App Password from Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('rahemanabdul696@gmail.com', 'Salman Fashions');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body); // Convert \n to <br> for HTML

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

