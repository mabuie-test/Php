<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error'=>'Acesso proibido']));
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error'=>'Método não permitido']));
}

$inp = json_decode(file_get_contents('php://input'), true) ?? [];
$id     = (int) ($inp['id'] ?? 0);
$status = $inp['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pendente','em progresso','concluido'], true)) {
    http_response_code(400);
    exit(json_encode(['error'=>'Dados inválidos']));
}

$stmt = $pdo->prepare("UPDATE solicitacoes SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $id])) {
    echo json_encode(['message'=>'Status atualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error'=>'Erro ao atualizar status']);
}
