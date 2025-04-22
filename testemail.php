<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'av.kitsgnt@gmail.com';
    $mail->Password   = 'your_app_password';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('av.kitsgnt@gmail.com', 'Test');
    $mail->addAddress('sunny70361@gmail.com');
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}