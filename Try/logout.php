<?php
require 'db.php';
header('Content-Type: application/json');

// Destrói sessão
$_SESSION = [];
session_destroy();

echo json_encode(['message' => 'Logout efetuado.']);
