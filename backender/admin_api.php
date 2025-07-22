<?php
// backend/admin_api.php
session_start();
require_once __DIR__ . '/db.php';

// Só admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$action = $_REQUEST['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {

  case 'list':
    $status = $_GET['status'] ?? '';
    $sql = "SELECT r.*, u.name AS client
            FROM requests r
            JOIN users u ON u.id = r.user_id
            WHERE (:status = '' OR r.status = :status)
            ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  case 'update_status':
    $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['id']]);
    // Auditoria
    $a = $pdo->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)");
    $a->execute([$_SESSION['user_id'], "Atualizou status do pedido #".$_POST['id']]);
    echo json_encode(['success' => true]);
    break;

  case 'create_admin':
    if ($_POST['secret'] !== 'SUA_CHAVE_SECRETA') {
      echo json_encode(['error' => 'Chave inválida']);
      exit;
    }
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?, 'admin')");
    $stmt->execute([$_POST['name'], $_POST['email'], $hash]);
    $a = $pdo->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)");
    $a->execute([$_SESSION['user_id'], "Criou admin #".$pdo->lastInsertId()]);
    echo json_encode(['success' => true]);
    break;

  case 'audit':
    $stmt = $pdo->query("
      SELECT a.*, u.name AS user_name
      FROM audit_logs a
      LEFT JOIN users u ON u.id = a.user_id
      ORDER BY a.created_at DESC
      LIMIT 100
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  default:
    echo json_encode(['error' => 'Ação inválida']);
}
