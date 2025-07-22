<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // validação básica
    if (empty($name) || empty($email) || empty($password)) {
        http_response_code(400);
        echo 'Preencha todos os campos.';
        exit;
    }

    // verificar se já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo 'Email já registado.';
        exit;
    }

    // inserir
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $hash])) {
        echo 'Registo efetuado com sucesso.';
    } else {
        http_response_code(500);
        echo 'Erro no servidor.';
    }
}
