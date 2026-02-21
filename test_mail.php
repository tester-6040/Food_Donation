<?php

declare(strict_types=1);

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    exit("❌ vendor/autoload.php not found. Run: composer install\n");
}
require_once $autoload;

use Core\Mailer;

$config = require __DIR__ . '/config/app.php';

$result = Mailer::send(
    'balaabinesh0@gmail.com',
    'Test Email from PHPMailer',
    '<h2>SMTP working ✅</h2><p>This is a test email.</p>',
    $config['mail']
);

echo $result
    ? "✅ Email sent successfully\n"
    : "❌ Email failed (check storage/mail.log)\n";
