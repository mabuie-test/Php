<?php
require 'db.php';

// Se o Content-Type for JSON, decodifica e funde em $_POST
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) {
        $_POST = array_merge($_POST, $json);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =        $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        http_response_code(400);
        echo 'Preencha todos os campos.';
        exit;
    }

    // Verifica se já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo 'Email já registado.';
        exit;
    }

    // Insere novo utilizador
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $hash])) {
        echo 'Registo efetuado com sucesso.';
    } else {
        http_response_code(500);
        echo 'Erro no servidor.';
    }
}
