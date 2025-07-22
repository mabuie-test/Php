<?php
session_start();
$_SESSION = [];
session_destroy();

// Se estás a usar fetch, devolve JSON; caso contrário, redireciona:
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Logout efetuado']);
} else {
    header('Location: ../public/login.html');
}
