<?php
require 'db.php';
header('Content-Type: application/json');

// Fundir JSON caso venha nesse formato
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) {
        $_POST = array_merge($_POST, $json);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos.']);
    exit;
}

// Verifica duplicado
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email já registado.']);
    exit;
}

// Insere
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
if ($stmt->execute([$name, $email, $hash])) {
    echo json_encode(['message' => 'Registo efetuado com sucesso.']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor ao registar utilizador.']);
}
