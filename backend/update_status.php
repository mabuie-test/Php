<?php
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso proibido']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$id     = (int) ($data['id'] ?? 0);
$status = $data['status'] ?? '';
$valid_status = ['pendente','em progresso','concluido'];

if (!$id || !in_array($status, $valid_status, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $id])) {
    echo json_encode(['message' => 'Status atualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor']);
}
