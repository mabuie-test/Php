<?php
session_start();
if($_SESSION['role']!=='admin') header('Location:login.php');
?>
<!-- Cola TODO o admin.html, mantendo tabela e botões de status -->
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Painel Admin | PHIL ASEAN</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    :root {
      --primary: #0056b3;
      --success: #28a745;
      --accent: #ffc107;
      --dark: #212529;
      --light: #f8f9fa;
      --error: #dc3545;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: var(--light);
      color: var(--dark);
    }
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 1rem 0;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--primary);
      position: static !important;
      background: #fff;
    }
    header h1 {
      font-size: 1.75rem;
      margin: 0;
    }
    #logoutBtn {
      background: var(--primary);
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
    }
    #controls {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin: 1rem 0;
    }
    #controls input,
    #controls select,
    #controls button {
      padding: 0.5rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    #controls button {
      background: var(--primary);
      color: #fff;
      border: none;
      cursor: pointer;
    }
    .table-responsive {
      overflow-x: auto;
      margin-bottom: 2rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #ccc;
      text-align: left;
      vertical-align: top;
    }
    th {
      background: var(--primary);
      color: #fff;
    }
    .status-cell.pending    { color: var(--error); }
    .status-cell.in_progress{ color: var(--accent); }
    .status-cell.completed  { color: var(--success); }
    .history-list {
      list-style: none;
      padding-left: 0;
      margin: 0;
      font-size: 0.85rem;
      color: #333;
    }
    .history-list li {
      margin-bottom: 0.25rem;
    }
    .btn-update,
    .btn-download,
    .btn {
      padding: 0.4rem 0.8rem;
      font-size: 0.9rem;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      margin-top: 0.5rem;
    }
    .btn-update { background: var(--success); color: #fff; }
    .btn-download { background: var(--accent); color: #fff; }
    /* Gestão de Admins */
    #user-management {
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 2px solid var(--primary);
    }
    #user-management h2 {
      margin-bottom: 1rem;
      font-size: 1.5rem;
      color: var(--primary);
    }
    #createAdminForm {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    #createAdminForm input {
      flex: 1 1 200px;
      padding: 0.5rem;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    #createAdminForm .btn {
      background: var(--primary);
      color: #fff;
    }
    #adminList {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    #adminList li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 4px;
      margin-bottom: 0.5rem;
    }
    /* Auditoria */
    #audit-log {
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 2px solid var(--primary);
    }
    #audit-log h2 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: var(--primary);
    }
    #auditList {
      list-style: none;
      padding: 0;
      margin: 0;
      max-height: 300px;
      overflow-y: auto;
      border: 1px solid #ccc;
      background: #fff;
      border-radius: 4px;
    }
    #auditList li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 1rem;
      border-bottom: 1px solid #eee;
      font-size: 0.9rem;
      color: var(--dark);
    }
    #auditList li:nth-child(odd) { background: #fdfdfd; }
    #auditList li:last-child { border-bottom: none; }
    @media (max-width: 768px) {
      #controls { flex-direction: column; }
      #controls input, #controls select, #controls button { width: 100%; }
      table { min-width: 100%; }
      #createAdminForm { flex-direction: column; }
      #createAdminForm input, #createAdminForm .btn { width: 100%; }
      #adminList li, #auditList li { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body> 
  <div class="container">
    <header>
      <h1>Painel de Administração</h1>
      <button id="logoutBtn">Logout</button>
    </header>

    <div id="controls">
      <input type="text" id="searchInput" placeholder="Buscar por cliente, serviço ou referência…">
      <select id="statusFilter">
        <option value="">Todos os Status</option>
        <option value="pending">Pendente</option>
        <option value="in_progress">Em Progresso</option>
        <option value="completed">Concluído</option>
      </select>
      <button id="refreshBtn">Atualizar Lista</button>
    </div>

    <div class="table-responsive">
      <table id="adminTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Serviços</th>
            <th>Status</th>
            <th>Histórico</th>
            <th>Fatura</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <!-- Gestão de Administradores -->
    <section id="user-management">
      <h2>Gestão de Administradores</h2>
      <form id="createAdminForm">
        <input name="name" placeholder="Nome" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <input name="secret" type="password" placeholder="Chave de criação" required>
        <button type="submit" class="btn">Criar Admin</button>
      </form>
      <ul id="adminList"></ul>
    </section>

    <!-- Registos da Auditoria -->
    <section id="audit-log">
      <h2>Registos da Auditoria</h2>
      <ul id="auditList"></ul>
    </section>
  </div>

  <script src="admin.js" defer></script>
</body>
</html>
