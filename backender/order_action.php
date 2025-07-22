<?php
// backend/order_action.php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$company = $_POST['company'] ?? '';
$vessel  = $_POST['vessel'];
$port    = $_POST['port'];
$date    = $_POST['date'];
$services= json_encode($_POST['services'] ?? []);
$notes   = $_POST['notes'] ?? '';

// Insere pedido
$stmt = $pdo->prepare("
  INSERT INTO requests
    (user_id,company,vessel,port,date_estimated,services,notes)
  VALUES (?,?,?,?,?,?,?)
");
$stmt->execute([$user_id, $company, $vessel, $port, $date, $services, $notes]);
$request_id = $pdo->lastInsertId();

// Auditoria
$a = $pdo->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)");
$a->execute([$user_id, "Criou pedido #$request_id"]);

// Envio de email
$to      = 'philasean@philaseanprovider.co.mz';
$subject = "Novo pedido #$request_id de $vessel";
$message = "Detalhes do pedido:\nID: $request_id\nCliente ID: $user_id\nServiços: $services\nPorto: $port\nData Estimada: $date";
$headers = "From: no-reply@seusite.com\r\n";
mail($to, $subject, $message, $headers);

// Redireciona
header('Location: reserva.php?success=1');
exit;
