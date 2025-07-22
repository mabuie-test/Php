<?php
require 'db.php';

// Lê JSON se for o caso
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $inp = json_decode(file_get_contents('php://input'), true);
    if (is_array($inp)) $_POST = array_merge($_POST, $inp);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido');
}

$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    exit('Preencha todos os campos.');
}

$stmt = $pdo->prepare("
    SELECT id, nome, senha_hash, is_admin
    FROM users
    WHERE email = ?
");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['senha_hash'])) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['nome'];
    $_SESSION['role']      = $user['is_admin'] ? 'admin' : 'user';
    echo 'Login bem‑sucedido.';
} else {
    http_response_code(401);
    exit('Credenciais inválidas.');
}
