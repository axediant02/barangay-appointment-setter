<?php

$charset = 'utf8mb4';

/*
|--------------------------------------------------------------------------
| Railway Database Detection
|--------------------------------------------------------------------------
| Priority:
| 1. DATABASE_URL (new Railway standard)
| 2. MYSQL* variables
| 3. DB_* variables
|--------------------------------------------------------------------------
*/

$url = getenv('DATABASE_URL');

if ($url) {
    // Parse DATABASE_URL
    $dbopts = parse_url($url);

    $host = $dbopts['host'] ?? null;
    $port = $dbopts['port'] ?? 3306;
    $user = $dbopts['user'] ?? null;
    $pass = $dbopts['pass'] ?? null;
    $db   = isset($dbopts['path']) ? ltrim($dbopts['path'], '/') : null;

} else {
    // Fallback to individual env variables
    $host = getenv('MYSQLHOST') ?: getenv('DB_HOST');
    $port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306;
    $db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME');
    $user = getenv('MYSQLUSER') ?: getenv('DB_USER');
    $pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD');
}

// 🚨 STOP if variables are missing (prevents localhost fallback in production)
if (!$host || !$user || !$db) {
    error_log('Database environment variables are missing.');
    http_response_code(503);
    die('Database configuration error.');
}

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 5,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {

    // Log full error internally
    error_log('Database connection failed: ' . $e->getMessage());

    // Show safe public message
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');

    die('<!DOCTYPE html>
    <html>
    <head>
        <title>Service Unavailable</title>
    </head>
    <body>
        <h1>Service Unavailable</h1>
        <p>Database connection failed. Please try again later.</p>
    </body>
    </html>');
}