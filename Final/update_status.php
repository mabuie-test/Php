<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error'=>'Acesso proibido']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    exit;
}

// lê JSON
$json = json_decode(file_get_contents('php://input'), true);
$id     = (int) ($json['id'] ?? 0);
$status = $json['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pendente','em progresso','concluido'], true)) {
    http_response_code(400);
    echo json_encode(['error'=>'Dados inválidos']);
    exit;
}

$stmt = $pdo->prepare("UPDATE solicitacoes SET status = ? WHERE id = ?");
$ok = $stmt->execute([$status, $id]);
if ($ok) {
    echo json_encode(['message'=>'Status atualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error'=>'Erro ao atualizar status']);
}
