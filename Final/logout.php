<?php
require 'db.php';

$_SESSION = [];
session_destroy();

if (headers_sent()) {
    echo 'Logout efetuado.';
} else {
    header('Location: ../public/login.php');
}
