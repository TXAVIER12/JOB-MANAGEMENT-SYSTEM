<?php
require_once 'functions.php';
require_login();
$user = current_user();

// Date filter
$from = $_GET['from'] ?? date('Y-m-d');
$to = $_GET['to'] ?? date('Y-m-d');

// Fetch sales between dates (inclusive)
$start = $from . ' 00:00:00';
$end = $to . ' 23:59:59';

$stmt = $pdo->prepare('SELECT s.*, pr.name FROM sales s JOIN products pr ON pr.id = s.product_id WHERE s.user_id=? AND s.created_at BETWEEN ? AND ? ORDER BY s.created_at ASC');
$stmt->execute([$user['id'], $start, $end]);
$sales = $stmt->fetchAll();

$total_profit = 0;
foreach ($sales as &$s) {
    $latest_purchase = latest_purchase_price($user['id'], $s['product_id']);
    $product_stmt = $pdo->prepare('SELECT pieces_per_pack FROM products WHERE id = ?');
    $product_stmt->execute([$s['product_id']]);
    $product = $product_stmt->fetch();
    $s['profit'] = calculate_profit($s, $product, $latest_purchase);
    $total_profit += $s['profit'];
}
unset($s);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reports - Job Manager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="wrap">
    <aside class="sidebar">
      <h3>Job Manager</h3>
      <p><?=htmlspecialchars($user['name'])?></p>
      <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="purchases.php">Purchases</a>
        <a href="sales.php">Sales</a>
        <a href="reports.php">Reports</a>
        <a href="reset_password.php">Reset Password</a>
        <a href="logout.php">Logout</a>
      </nav>
    </aside>
    <main class="main">
      <h2>Reports</h2>
      <form method="get" class="inline">
        <label>From</label>
        <input type="date" name="from" value="<?=htmlspecialchars($from)?>">
        <label>To</label>
        <input type="date" name="to" value="<?=htmlspecialchars($to)?>">
        <button>Filter</button>
      </form>

      <div class="card">Total Profit (<?=htmlspecialchars($from)?> to <?=htmlspecialchars($to)?>): <strong><?=number_format($total_profit,2)?></strong></div>

      <section>
        <h3>Sales</h3>
        <?php if (empty($sales)): ?>
          <div>No sales in this range.</div>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Date</th><th>Product</th><th>Unit</th><th>Qty</th><th>Sale Price</th><th>Profit</th></tr></thead>
            <tbody>
            <?php foreach($sales as $s): ?>
              <tr>
                <td><?=htmlspecialchars($s['created_at'])?></td>
                <td><?=htmlspecialchars($s['name'])?></td>
                <td><?=htmlspecialchars($s['unit_type'])?></td>
                <td><?=htmlspecialchars($s['quantity'])?></td>
                <td><?=number_format($s['sale_price'],2)?></td>
                <td><?=number_format($s['profit'],2)?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
