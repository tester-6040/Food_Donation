<?php

declare(strict_types=1);

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$detectedBasePath = '';
if (is_string($scriptName) && $scriptName !== '') {
    $dir = str_replace('\\', '/', dirname($scriptName));
    $detectedBasePath = $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
}

return [
    'app_name' => getenv('APP_NAME') ?: 'Food Donation Platform',
    'base_url' => getenv('APP_URL') ?: '',
    'base_path' => rtrim(getenv('APP_BASE_PATH') ?: $detectedBasePath, '/'),
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'database' => getenv('DB_NAME') ?: 'food_donation',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
        'port' => (int) (getenv('MAIL_PORT') ?: 587),
        'username' => getenv('MAIL_USERNAME') ?: 'noreplyfooddonation123@gmail.com',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM') ?: 'noreplyfooddonation123@gmail.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Food Donation Platform',
        'admin_email' => getenv('ADMIN_EMAIL') ?: 'balaabineshh0@gmail.com',
        'secondary_admin_email' => getenv('SECONDARY_ADMIN_EMAIL') ?: 'balaabinesh88@gmail.com',
        'admin_recipients' => [
            getenv('ADMIN_EMAIL') ?: 'balaabineshh0@gmail.com',
            getenv('SECONDARY_ADMIN_EMAIL') ?: 'balaabinesh88@gmail.com',
        ],
        'log_path' => __DIR__ . '/../storage/mail.log',
    ],
];
