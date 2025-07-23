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
  <title>Login – PHIL ASEAN PROVIDER & LOGISTICS</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <main>
    <h2>Login</h2>
    <form id="loginForm">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button type="submit">Entrar</button>
    </form>
  </main>
  <script src="script.js" defer></script>
</body>
</html>
