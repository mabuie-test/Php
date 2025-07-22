<?php
session_start();

$host   = 'localhost';
$dbname = 'phil_asean';      // nome da BD
$user   = 'seu_usuario_mysql';
$pass   = 'sua_password_mysql';
$charset= 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  exit("Erro na conexão: " . $e->getMessage());
}

