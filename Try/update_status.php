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

$id     = (int) ($_POST['id']     ?? 0);
$status =        $_POST['status'] ?? '';

$valid = ['pendente','em progresso','concluido'];
if ($id <= 0 || !in_array($status, $valid, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $id])) {
    echo json_encode(['message' => 'Status atualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao atualizar status']);
}
