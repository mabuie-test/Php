<?php
require 'db.php';

// Só POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido');
}

// Lê JSON do corpo
$input = json_decode(file_get_contents('php://input'), true);

// Usa o JSON ou fallback em $_POST
$email    = trim($input['email']    ?? $_POST['email']    ?? '');
$password =      $input['password'] ?? $_POST['password'] ?? '';

// Validação
if ($email === '' || $password === '') {
    http_response_code(400);
    exit('Preencha todos os campos.');
}

// Consulta usuário
$stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['role']      = $user['role'];
    echo json_encode(['message' => 'Login bem‑sucedido.']);
} else {
    http_response_code(401);
    exit('Credenciais inválidas.');
}
