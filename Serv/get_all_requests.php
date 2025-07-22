<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error'=>'Acesso proibido']));
}

$stmt = $pdo->query("
    SELECT s.id, u.nome AS user_name, u.email,
           s.nome_solicitante AS name, s.empresa, s.email_solicitante AS email_solicitante,
           s.telefone, s.navio, s.porto, s.data_estimada AS date,
           s.servicos, s.observacoes AS notes, s.status, s.created_at
    FROM solicitacoes s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.created_at DESC
");
echo json_encode($stmt->fetchAll());
