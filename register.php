<?php
require 'db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD']!=='POST') {
  http_response_code(405); exit(json_encode(['error'=>'Método não permitido']));
}
$in   = json_decode(file_get_contents('php://input'), true);
$name = trim($in['name']     ?? '');
$email= trim($in['email']    ?? '');
$pass =      $in['password'] ?? '';
if ($name==='' || $email==='' || $pass==='') {
  http_response_code(400);
  exit(json_encode(['error'=>'Preencha todos os campos.']));
}
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
  http_response_code(409);
  exit(json_encode(['error'=>'Email já registado.']));
}
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
if ($stmt->execute([$name,$email,$hash])) {
  echo json_encode(['message'=>'Registo efetuado com sucesso.']);
} else {
  http_response_code(500);
  exit(json_encode(['error'=>'Erro no servidor.']));
}
