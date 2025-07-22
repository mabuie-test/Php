<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email||!$password) {
  http_response_code(400); echo 'Preencha todos os campos.'; exit;
}

$stmt = $pdo->prepare("SELECT id,name,password,role FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
  $_SESSION['user_id']   = $user['id'];
  $_SESSION['user_name'] = $user['name'];
  $_SESSION['role']      = $user['role'];
  echo 'Login bem‑sucedido.';
} else {
  http_response_code(401); echo 'Credenciais inválidas.';
}
