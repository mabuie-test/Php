<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <!-- resto do HEAD idêntico a admin.html :contentReference[oaicite:6]{index=6} -->
</head>
<body>
  <div class="container">
    <header>
      <h1>Painel de Administração</h1>
      <button id="logoutBtn">Logout</button>
    </header>

    <div id="controls">…</div>
    <div class="table-responsive">
      <table id="adminTable">…</table>
    </div>

    <section id="user-management">…</section>
    <section id="audit-log">…</section>
  </div>

  <script src="admin.js" defer></script>
</body>
</html>
