<?php
require 'db.php';
header('Content-Type: application/json');
if ($_SESSION['role']!=='admin') { http_response_code(403); exit; }
$stmt = $pdo->query("
  SELECT r.id,u.name AS user_name,u.email,
         r.service_type,r.description,r.status,r.created_at
  FROM requests r
  JOIN users u ON r.user_id=u.id
  ORDER BY r.created_at DESC
");
echo json_encode($stmt->fetchAll());
