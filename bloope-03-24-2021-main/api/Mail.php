<?php
require_once('PHPMailer/PHPMailerAutoload.php');
class Mail {
    public static function sendMail($subject, $body, $address) {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = 'ezeakudoluchinemerem@gmail.com';
        $mail->Password = '&&&&&&&';
        $mail->SetFrom('no-reply@bloope.org');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($address);

        $mail->Send();
    }
}
?>