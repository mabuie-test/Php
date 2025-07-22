<?php
// backend/login_action.php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$email = trim($_POST['email']);
$pass  = $_POST['password'];

$stmt = $pdo->prepare("SELECT id,password_hash,role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($pass, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $destino = ($user['role'] === 'admin') ? 'admin.php' : 'reserva.php';
    header("Location: $destino");
} else {
    header('Location: login.php?error=1');
}
exit;
