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

// Verifica sessão
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$service_type = trim($_POST['service_type'] ?? '');
$description  = trim($_POST['description']  ?? '');

if ($service_type === '' || $description === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Campos em falta']);
    exit;
}

// Insere pedido
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
