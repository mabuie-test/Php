<?php
session_start();

// Configuração da BD
$host    = 'localhost';
$dbname  = 'philaded_Philaseanproviderwebsite';
$user    = 'philaded_Philaseanproviderwebsite';
$pass    = 'Philaseanproviderwebsite';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Falha na conexão com a BD: ' . $e->getMessage()]);
    exit;
}
