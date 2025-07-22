<?php
require 'db.php';

// só POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    exit;
}

// autenticação
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'Não autorizado']);
    exit;
}

// lê o payload (Form URL Encoded ou JSON)
$data = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $raw = json_decode(file_get_contents('php://input'), true);
    if (is_array($raw)) $data = $raw;
} else {
    $data = $_POST;
}

// valida campos essenciais
$required = ['name','email','vessel','port','date','services'];
foreach ($required as $f) {
    if (empty($data[$f])) {
        http_response_code(400);
        echo json_encode(['error'=>"Campo '{$f}' em falta"]);
        exit;
    }
}

// monta objeto detalhes
$details = [
    'name'    => $data['name'],
    'company' => $data['company'] ?? '',
    'email'   => $data['email'],
    'phone'   => $data['phone'] ?? '',
    'vessel'  => $data['vessel'],
    'port'    => $data['port'],
    'date'    => $data['date'],
    'services'=> is_array($data['services']) ? $data['services'] : [$data['services']],
    'notes'   => $data['notes'] ?? ''
];

// insere solicitação
$stmt = $pdo->prepare("
    INSERT INTO solicitacoes (user_id, detalhes)
    VALUES (:uid, :det)
");
$ok = $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':det' => json_encode($details, JSON_UNESCAPED_UNICODE)
]);

if ($ok) {
    echo json_encode(['message'=>'Pedido submetido com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['error'=>'Erro ao inserir a solicitação']);
}
