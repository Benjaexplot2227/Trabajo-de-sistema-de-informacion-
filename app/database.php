<?php
declare(strict_types=1);

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('TECNO_DB_HOST') ?: '127.0.0.1';
    $port = getenv('TECNO_DB_PORT') ?: '3306';
    $name = getenv('TECNO_DB_NAME') ?: 'tecnostock';
    $user = getenv('TECNO_DB_USER') ?: 'root';
    $password = getenv('TECNO_DB_PASSWORD') ?: '';
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $connection = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}

