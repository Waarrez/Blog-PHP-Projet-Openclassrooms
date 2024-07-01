<?php

namespace Root\P5\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mailer;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
        $this->configureMailer();
    }

    private function configureMailer(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'thimote.cabotte6259@gmail.com';
        $this->mailer->Password = 'nbqwlfdnkerqgypj';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';
    }

    public function sendMail(string $name, string $email, string $message): bool
    {
        try {
            $this->mailer->setFrom($email, $name);
            $this->mailer->addAddress('thimote.cabotte6259@gmail.com');
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Nouveau message blog php';
            $this->mailer->Body = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
