<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// 1) Verifica sessão
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// 2) Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// 3) Lê payload (JSON ou form‑urlencoded)
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $data = $_POST;
}

// 4) Normaliza services em array
$servicesRaw = $data['services'] ?? [];
if (!is_array($servicesRaw)) {
    $servicesRaw = [$servicesRaw];
}

// 5) Monta objeto detalhes incluindo services
$details = [
    'name'     => $data['name']     ?? '',
    'company'  => $data['company']  ?? '',
    'email'    => $data['email']    ?? '',
    'phone'    => $data['phone']    ?? '',
    'vessel'   => $data['vessel']   ?? '',
    'port'     => $data['port']     ?? '',
    'date'     => $data['date']     ?? '',
    'services' => $servicesRaw,
    'notes'    => $data['notes']    ?? ''
];

// 6) Insere na tabela solicitacoes
try {
    $stmt = $pdo->prepare("
        INSERT INTO solicitacoes (user_id, detalhes)
        VALUES (:uid, :det)
    ");
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':det' => json_encode($details, JSON_UNESCAPED_UNICODE)
    ]);
    echo json_encode(['message' => 'Pedido submetido com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao inserir a solicitação: ' . $e->getMessage()]);
}
