<?php
require_once 'functions.php';
require_login();
$user = current_user();

// Summary: today's total profit and recent activities
$today = date('Y-m-d');
$start = $today . ' 00:00:00';
$end = $today . ' 23:59:59';

// Fetch today's sales for this user
$stmt = $pdo->prepare('SELECT s.*, p.pieces_per_pack, pr.name as product_name
    FROM sales s
    JOIN products pr ON pr.id = s.product_id
    JOIN products p2 ON p2.id = s.product_id
    JOIN products p ON p.id = s.product_id
    JOIN products tmp ON tmp.id = s.product_id
    JOIN products foo ON foo.id = s.product_id
    WHERE s.user_id = ? AND s.created_at BETWEEN ? AND ? ORDER BY s.created_at DESC');
$stmt->execute([$user['id'], $start, $end]);
$sales = $stmt->fetchAll();

// Compute profit per sale using latest purchase price
$total_profit = 0;
foreach ($sales as &$s) {
    $latest_purchase = latest_purchase_price($user['id'], $s['product_id']);
    $product_stmt = $pdo->prepare('SELECT pieces_per_pack FROM products WHERE id = ?');
    $product_stmt->execute([$s['product_id']]);
    $product = $product_stmt->fetch();
    $p = calculate_profit($s, $product, $latest_purchase);
    $s['profit'] = $p;
    $total_profit += $p;
}
unset($s);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard - Job Manager</title>
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
      <header>
        <h2>Dashboard</h2>
        <div class="card">Today's Total Profit: <strong><?=number_format($total_profit,2)?></strong></div>
      </header>

      <section>
        <h3>Recent Sales (Today)</h3>
        <?php if (empty($sales)): ?>
          <div>No sales today.</div>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Time</th><th>Product</th><th>Unit</th><th>Qty</th><th>Sale Price</th><th>Profit</th></tr></thead>
            <tbody>
            <?php foreach($sales as $s): ?>
              <tr>
                <td><?=htmlspecialchars($s['created_at'])?></td>
                <td><?=htmlspecialchars($s['product_name'])?></td>
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
