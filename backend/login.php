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
    $email    = trim($_POST['email']    ?? '');
    $password =        $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo 'Preencha todos os campos.';
        exit;
    }

    // Consulta utilizador
    $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Inicia sessão
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];
        echo 'Login bem‑sucedido.';
    } else {
        http_response_code(401);
        echo 'Credenciais inválidas.';
    }
}
