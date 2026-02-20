<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$detectedBasePath = '';
if (is_string($scriptName) && $scriptName !== '') {
    $dir = str_replace('\\', '/', dirname($scriptName));
    $detectedBasePath = $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
}

return [
    'app_name' => 'Food Donation Platform',
    'base_url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'base_path' => rtrim(getenv('APP_BASE_PATH') ?: $detectedBasePath, '/'),
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'food_donation',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'from' => getenv('MAIL_FROM') ?: 'noreply@ngo.local',
        'admin_recipients' => [
            'balaabineshh0@gmail.com',
            'balaabinesh88@gmail.com',
        ],
        'log_path' => __DIR__ . '/../storage/mail.log',
    ],
];
