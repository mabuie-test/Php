<?php
session_start();
if(isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<!-- cabeçalho idêntico ao login.html :contentReference[oaicite:0]{index=0} -->
<body>
  <header>…</header>

  <main>
    <div class="section-title"><h2>Login</h2></div>
    <form id="loginForm">
      <!-- inputs iguais aos originais :contentReference[oaicite:1]{index=1} -->
    </form>
  </main>

  <footer>…</footer>
  <script src="script.js" defer></script>
</body>
</html>
