<?php

declare(strict_types=1);

namespace Core;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function send(string $to, string $subject, string $message, array $mailConfig): bool
    {
        $logPath = $mailConfig['log_path'] ?? (__DIR__ . '/../storage/mail.log');

        if (class_exists(PHPMailer::class)) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = (string) ($mailConfig['host'] ?? '');
                $mail->SMTPAuth = true;
                $mail->Username = (string) ($mailConfig['username'] ?? '');
                $mail->Password = (string) ($mailConfig['password'] ?? '');
                $mail->Port = (int) ($mailConfig['port'] ?? 587);
                $mail->SMTPSecure = (($mailConfig['encryption'] ?? 'tls') === 'tls')
                    ? PHPMailer::ENCRYPTION_STARTTLS
                    : PHPMailer::ENCRYPTION_SMTPS;

                $mail->setFrom((string) ($mailConfig['from_email'] ?? 'noreply@ngo.local'), (string) ($mailConfig['from_name'] ?? 'Food Donation Platform'));
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $message)));

                return $mail->send();
            } catch (Exception $e) {
                file_put_contents($logPath, '[' . date('c') . '] ERROR: ' . $mail->ErrorInfo . PHP_EOL, FILE_APPEND);
                return false;
            }
        }

        $headers = 'From: ' . ($mailConfig['from_email'] ?? 'noreply@ngo.local') . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8';
        $ok = @mail($to, $subject, $message, $headers);

        if (!$ok) {
            file_put_contents($logPath, '[' . date('c') . '] ERROR: mail() fallback failed TO:' . $to . ' SUBJECT:' . $subject . PHP_EOL, FILE_APPEND);
        }

        return $ok;
    }
}
