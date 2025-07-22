<?php
session_start();
if(!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>…</head>
<body>
  <header>…</header>

  <main>
    <!-- form de pedido idêntico ao reserva.html :contentReference[oaicite:4]{index=4} -->
    <form id="orderForm">
      <!-- mantém exatamente os mesmos campos, IDs e classes :contentReference[oaicite:5]{index=5} -->
    </form>

    <!-- secção histórico de pedidos -->
    <section id="history">…</section>
  </main>

  <footer>…</footer>
  <script src="script.js" defer></script>
</body>
</html>
