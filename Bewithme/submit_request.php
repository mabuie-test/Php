<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
if ($_SERVER['REQUEST_METHOD']!=='POST')    { http_response_code(405); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$stype = trim($data['service_type'] ?? '');
$desc  = trim($data['description']  ?? '');
if (!$stype||!$desc) { http_response_code(400); exit; }

$stmt = $pdo->prepare("
  INSERT INTO requests (user_id,service_type,description)
  VALUES (?, ?, ?)
");
if ($stmt->execute([$_SESSION['user_id'],$stype,$desc])) {
  echo json_encode(['message'=>'Pedido submetido com sucesso']);
} else {
  http_response_code(500);
}
