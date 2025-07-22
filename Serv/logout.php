<?php
require 'db.php';
$_SESSION = [];
session_destroy();
echo 'Logout efetuado.';
