<?php
// Include the PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // If you used Composer to install PHPMailer

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                           // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                              // Set the SMTP server to send through
    $mail->SMTPAuth = true;                                      // Enable SMTP authentication
    $mail->Username = 'onlinelms2000@gmail.com';                    // SMTP username (your Gmail email)
    $mail->Password = 'your-email-password';                     // SMTP password (your Gmail app password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption
    $mail->Port = 587;                                           // TCP port to connect to

    //Recipients
    $mail->setFrom('your-email@gmail.com', 'Mailer');            // From email and name
    $mail->addAddress('recipient@example.com', 'Joe User');      // Add a recipient (user’s email)

    // Content
    $mail->isHTML(true);                                         // Set email format to HTML
    $mail->Subject = 'New Notification';
    $mail->Body    = 'This is your new notification!';

    // Send email
    $mail->send();
    echo 'Notification has been sent.';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
