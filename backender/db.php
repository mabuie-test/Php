<?php
// backend/db.php
$host    = 'localhost';
$dbname  = 'philaded_Philaseanproviderwebsite';
$user    = 'philaded_Philaseanproviderwebsite';
$pass    = 'Philaseanproviderwebsite';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro na conexão com a base de dados: " . $e->getMessage());
}
