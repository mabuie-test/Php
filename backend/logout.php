<?php
require 'db.php';

$_SESSION = [];
session_destroy();

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Logout efetuado']);
} else {
    header('Location: ../public/login.php');
}
