<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Não autorizado';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método não permitido';
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$service_type = trim($data['service_type'] ?? '');
$description  = trim($data['description']  ?? '');

if (!$service_type || !$description) {
    http_response_code(400);
    echo 'Campos em falta';
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO requests (user_id, service_type, description)
    VALUES (:uid, :stype, :desc)
");
if ($stmt->execute([
    ':uid'   => $_SESSION['user_id'],
    ':stype' => $service_type,
    ':desc'  => $description
])) {
    echo json_encode(['message' => 'Pedido submetido com sucesso']);
} else {
    http_response_code(500);
    echo 'Erro no servidor';
}

