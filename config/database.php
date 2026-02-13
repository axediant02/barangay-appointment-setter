<?php
$host = 'db.fr-pari1.bengt.wasmernet.com';
$db   = 'dbJuJUVJFKkdfuLPao8bReZG';
$user = 'e844979b7e6780004a041c356833';
$pass = '0698e844-979b-7f79-8000-ac0db617c008';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}