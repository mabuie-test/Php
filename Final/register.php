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

$nome     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';

if ($nome === '' || $email === '' || $password === '') {
    http_response_code(400);
    exit('Preencha todos os campos.');
}

// verifica duplicado
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    exit('Email já registado.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("
    INSERT INTO users (nome, email, senha_hash)
    VALUES (?, ?, ?)
");
if ($stmt->execute([$nome, $email, $hash])) {
    echo 'Registo efetuado com sucesso.';
} else {
    http_response_code(500);
    exit('Erro no servidor.');
}
