<?php

namespace App\Notifications\Factory;

use App\Interfaces\Notifications\INotifier;
use App\Notifications\Providers\PHPMailerNotifier;
use InvalidArgumentException;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../../config.php';

class EmailProviderFactory
{
    public static function make(string $fromEmail, string $fromName, array $var_default_email, string $driver = DRIVEREMAIL): INotifier
    {
        return match (strtolower($driver)) {
            "phpmailer" => new PHPMailerNotifier(self::createPHPMailerInstance($fromEmail, $fromName), $var_default_email),
            default => throw new InvalidArgumentException("Provedor de email [$driver] não suportado.")
        };
    }

    public static function createPHPMailerInstance(string $fromEmail, string $fromName): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = HOST_EMAIL;
        $mailer->SMTPAuth = true;
        $mailer->Username = USERNAME_MAIL;
        $mailer->Password = PASSWORD_MAIL;
        $mailer->SMTPSecure = false;
        $mailer->SMTPAutoTLS = false;
        $mailer->CharSet = 'UTF-8';
        $mailer->Port = 587;
        $mailer->setFrom($fromEmail, $fromName);

        return $mailer;
    }
}
