<?php
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, service_type, description, status, created_at
    FROM requests
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll());
