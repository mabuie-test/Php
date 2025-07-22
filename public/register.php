<?php
session_start();
if(isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<!-- estrutura idêntica a register.html :contentReference[oaicite:2]{index=2} -->
<body>
  <header>…</header>

  <main>
    <div class="section-title"><h2>Registro</h2></div>
    <form id="registerForm">
      <!-- campos exactos do HTML que enviaste :contentReference[oaicite:3]{index=3} -->
    </form>
  </main>

  <footer>…</footer>
  <script src="script.js" defer></script>
</body>
</html>
