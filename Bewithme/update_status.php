<?php
require 'db.php';
header('Content-Type: application/json');
if ($_SESSION['role']!=='admin') { http_response_code(403); exit; }
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$id     = (int)($data['id'] ?? 0);
$status = $data['status'] ?? '';
$valid  = ['pendente','em progresso','concluido'];
if (!$id || !in_array($status,$valid,true)) {
  http_response_code(400); exit;
}

$stmt = $pdo->prepare("UPDATE requests SET status=? WHERE id=?");
if ($stmt->execute([$status,$id])) {
  echo json_encode(['message'=>'Status atualizado']);
} else {
  http_response_code(500);
}
