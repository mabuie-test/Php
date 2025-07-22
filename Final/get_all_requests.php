<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error'=>'Acesso proibido']);
    exit;
}

$stmt = $pdo->query("
    SELECT s.id, u.nome AS user_name, u.email,
           s.detalhes, s.status, s.created_at
    FROM solicitacoes s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
");
$rows = $stmt->fetchAll();

$out = [];
foreach ($rows as $r) {
    $d = json_decode($r['detalhes'], true);
    $out[] = [
        'id'         => $r['id'],
        'user_name'  => $r['user_name'],
        'email'      => $r['email'],
        'status'     => $r['status'],
        'created_at' => $r['created_at'],
        'details'    => $d
    ];
}

echo json_encode($out);
