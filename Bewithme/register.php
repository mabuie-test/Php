<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name||!$email||!$password) {
  http_response_code(400); echo 'Preencha todos os campos.'; exit;
}

// Verifica email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
  http_response_code(409); echo 'Email já registado.'; exit;
}

// Insere
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
if ($stmt->execute([$name,$email,$hash])) {
  echo 'Registo efetuado com sucesso.';
} else {
  http_response_code(500); echo 'Erro no servidor.';
}
