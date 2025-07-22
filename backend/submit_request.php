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

// 2) Verifica sessão de usuário
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// 3) Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// 4) Captura e valida dados
$service_type = trim($_POST['service_type'] ?? '');
$description  = trim($_POST['description']  ?? '');

if ($service_type === '' || $description === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Campos em falta']);
    exit;
}

// 5) Insere o pedido na BD
$stmt = $pdo->prepare("
    INSERT INTO requests (user_id, service_type, description)
    VALUES (:uid, :stype, :desc)
");

$ok = $stmt->execute([
    ':uid'   => $_SESSION['user_id'],
    ':stype' => $service_type,
    ':desc'  => $description
]);

if ($ok) {
    echo json_encode(['message' => 'Pedido submetido com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor ao inserir pedido']);
}
