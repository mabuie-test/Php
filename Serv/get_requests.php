<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error'=>'Não autorizado']));
}

$stmt = $pdo->prepare("
    SELECT id, nome_solicitante AS name, empresa, email_solicitante AS email,
           telefone, navio, porto, data_estimada AS date,
           servicos, observacoes AS notes, status, created_at
    FROM solicitacoes
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll());
