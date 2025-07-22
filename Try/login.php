<?php
require 'db.php';
header('Content-Type: application/json');

// Fundir JSON
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

$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos.']);
    exit;
}

// Busca utilizador
$stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciais inválidas.']);
    exit;
}

// Login bem‑sucedido
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['role']      = $user['role'];
echo json_encode(['message' => 'Login bem‑sucedido.']);
