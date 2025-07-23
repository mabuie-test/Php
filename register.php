<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registo – PHIL ASEAN PROVIDER & LOGISTICS</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <main>
    <h2>Registo</h2>
    <form id="registerForm">
      <label for="name">Nome</label>
      <input id="name" name="name" type="text" required>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button type="submit">Registar</button>
    </form>
  </main>
  <script src="script.js" defer></script>
</body>
</html>

