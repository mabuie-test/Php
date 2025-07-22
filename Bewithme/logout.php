<?php
require 'db.php';
$_SESSION = [];
session_destroy();
header('Location: ../public/login.php');
