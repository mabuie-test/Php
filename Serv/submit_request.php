<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error'=>'Método não permitido']));
}
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error'=>'Não autorizado']));
}

// Dados form-urlencoded
$data = $_POST;

// Validação básica
$req = ['name','email','vessel','port','date','services'];
foreach ($req as $f) {
    if (empty($data[$f])) {
        http_response_code(400);
        exit(json_encode(['error'=>"Campo '{$f}' em falta"]));
    }
}

// Normalização
$userId   = $_SESSION['user_id'];
$nome     = trim($data['name']);
$empresa  = trim($data['company'] ?? '');
$email    = trim($data['email']);
$telefone = trim($data['phone'] ?? '');
$navio    = trim($data['vessel']);
$porto    = trim($data['port']);
$dataEst  = trim($data['date']);
$services = is_array($data['services'])
            ? implode(', ', $data['services'])
            : trim($data['services']);
$notes    = trim($data['notes'] ?? '');

// Inserção
$stmt = $pdo->prepare("
    INSERT INTO solicitacoes
    (user_id, nome_solicitante, empresa, email_solicitante,
     telefone, navio, porto, data_estimada, servicos, observacoes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if ($stmt->execute([
    $userId, $nome, $empresa, $email,
    $telefone, $navio, $porto, $dataEst,
    $services, $notes
])) {
    echo json_encode(['message'=>'Pedido submetido com sucesso']);
} else {
    http_response_code(500);
    exit(json_encode(['error'=>'Erro ao inserir solicitação']));
}
