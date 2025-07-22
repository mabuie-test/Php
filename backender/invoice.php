<?php
// backend/invoice.php
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("
  SELECT r.*, u.name, u.email
  FROM requests r
  JOIN users u ON u.id = r.user_id
  WHERE r.id = ?
");
$stmt->execute([$id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    die('Pedido não encontrado.');
}

// Exemplo simples de fatura em HTML
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Fatura #<?= $id ?></title></head>
<body>
  <h1>Fatura #<?= $id ?></h1>
  <p><strong>Cliente:</strong> <?= htmlspecialchars($request['name']) ?> (<?= htmlspecialchars($request['email']) ?>)</p>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr><th>Serviços</th><th>Quantidade</th><th>Preço Unit.</th><th>Total</th></tr>
    <?php
      $items = json_decode($request['services'], true) ?: [];
      // Supondo que cada serviço venha no formato ["serviço","quantidade","preço"]
      $grandTotal = 0;
      foreach ($items as $it) {
        list($svc,$qty,$unit) = $it;
        $total = $qty * $unit;
        $grandTotal += $total;
        echo "<tr>
                <td>".htmlspecialchars($svc)."</td>
                <td>$qty</td>
                <td>".number_format($unit,2)."</td>
                <td>".number_format($total,2)."</td>
              </tr>";
      }
    ?>
    <tr>
      <td colspan="3" align="right"><strong>Total Geral:</strong></td>
      <td><strong><?= number_format($grandTotal,2) ?></strong></td>
    </tr>
  </table>
</body>
</html>
