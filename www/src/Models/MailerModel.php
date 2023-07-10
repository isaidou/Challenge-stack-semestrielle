<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerModel extends Model
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP(); // Send using SMTP
        $this->mail->Host = SMTP_HOST; // Set the SMTP server to send through
        $this->mail->SMTPAuth = true; // Enable SMTP authentication
        $this->mail->Username = SMTP_USERNAME;
        $this->mail->Password = SMTP_PASSWORD; // SMTP password
        $this->mail->SMTPSecure = 'tls'; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $this->mail->Port = SMTP_PORT; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $this->mail->setFrom(SMTP_MAIL, SITENAME);
        $this->mail->isHTML(true);
    }

    public function sendMail(string $to, string $subject, string $body, string $alt = ''): bool
    {
        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $alt;

            return $this->mail->send();
        } catch (Exception $e) {
            // Handle exception
            return false;
        }
    }
}
