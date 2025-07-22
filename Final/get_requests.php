<?php
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'Não autorizado']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, detalhes, status, created_at
    FROM solicitacoes
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

$out = [];
foreach ($rows as $r) {
    $d = json_decode($r['detalhes'], true);
    $out[] = [
        'id'         => $r['id'],
        'created_at' => $r['created_at'],
        'status'     => $r['status'],
        'details'    => $d
    ];
}

echo json_encode($out);
