<?php
// backend/register_action.php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$name  = trim($_POST['name']);
$email = trim($_POST['email']);
$pass  = $_POST['password'];

$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash) VALUES (?,?,?)");
$stmt->execute([$name, $email, $hash]);

$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['role']    = 'client';

header('Location: reserva.php');
exit;
