<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server SMTP Settings
    $mail->isSMTP();
    // $mail->Host = 'mail.nhsurulere.site';
    $mail->Host = 'mail.6bytes.site';
    $mail->SMTPAuth = true;
    // $mail->Username = '6bytes@nhsurulere.site';
    $mail->Username = 'info@6bytes.site';
    $mail->Password = 'Qwertyuiop@1?';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

} catch(\Exception $e) {
    echo "Mail not sent: {$mail->ErrorInfo}";
}