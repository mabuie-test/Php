<?php
require 'db.php';

// 1) Detecta JSON e funde em $_POST
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $json = json_decode(file_get_contents('php://input'), true);
    if (is_array($json)) {
        $_POST = array_merge($_POST, $json);
    }
}

// 2) Só processa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =        $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        http_response_code(400);
        echo 'Preencha todos os campos.';
        exit;
    }

    // 3) Busca utilizador
    $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 4) Verifica password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];
        echo 'Login bem‑sucedido.';
    } else {
        http_response_code(401);
        echo 'Credenciais inválidas.';
    }
}
?>
