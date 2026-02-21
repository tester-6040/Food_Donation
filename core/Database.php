<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/../config/app.php';
        $db = $config['db'];

        $host = $db['host'] ?? '127.0.0.1';
        $port = (string) ($db['port'] ?? '3306');
        $name = $db['name'] ?? ($db['database'] ?? 'food_donation');
        $user = $db['user'] ?? ($db['username'] ?? 'root');
        $pass = $db['pass'] ?? ($db['password'] ?? '');
        $charset = $db['charset'] ?? 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

        self::$pdo = new PDO($dsn, (string) $user, (string) $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$pdo;
    }
}
