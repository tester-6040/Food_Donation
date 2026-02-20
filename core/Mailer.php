<?php

class Mailer
{
    private array $config;

    public function __construct()
    {
        $app = require __DIR__ . '/../config/app.php';
        $this->config = $app['mail'];
    }

    public function send(array|string $to, string $subject, string $message): void
    {
        $recipients = array_values(array_unique(array_filter((array) $to)));
        if (empty($recipients)) {
            return;
        }

        $headers = 'From: ' . $this->config['from'] . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';

        foreach ($recipients as $email) {
            $ok = @mail($email, $subject, $message, $headers);
            if (!$ok) {
                $this->logFallback($email, $subject, $message);
            }
        }
    }

    private function logFallback(string $email, string $subject, string $message): void
    {
        $line = sprintf("[%s] TO:%s | SUBJECT:%s | BODY:%s\n", date('c'), $email, $subject, str_replace(["\r", "\n"], ' ', $message));
        file_put_contents($this->config['log_path'], $line, FILE_APPEND);
    }
}
