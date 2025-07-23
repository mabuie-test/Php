<?php
require 'db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD']!=='POST') {
  http_response_code(405); exit(json_encode(['error'=>'Método não permitido']));
}
$in = json_decode(file_get_contents('php://input'), true);
$email    = trim($in['email']    ?? '');
$password =      $in['password'] ?? '';
if ($email === '' || $password === '') {
  http_response_code(400);
  exit(json_encode(['error'=>'Preencha todos os campos.']));
}
$stmt = $pdo->prepare("SELECT id,name,password,role FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password'])) {
  $_SESSION['user_id']   = $user['id'];
  $_SESSION['user_name'] = $user['name'];
  $_SESSION['role']      = $user['role'];
  echo json_encode(['message'=>'Login bem‑sucedido.']);
} else {
  http_response_code(401);
  exit(json_encode(['error'=>'Credenciais inválidas.']));
}
