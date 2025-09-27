<?php
require_once 'functions.php';
require_login();
$user = current_user();

// Handle new sale and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_sale'])) {
        $product_id = (int)post('product_id');
        $unit_type = post('unit_type', 'pack') === 'piece' ? 'piece' : 'pack';
        $quantity = (int)post('quantity');
        $sale_price = (float)post('sale_price');
        $stmt = $pdo->prepare('INSERT INTO sales (user_id, product_id, unit_type, quantity, sale_price) VALUES (?,?,?,?,?)');
        $stmt->execute([$user['id'], $product_id, $unit_type, $quantity, $sale_price]);
        header('Location: sales.php');
        exit;
    }
    if (isset($_POST['delete_sale'])) {
        $id = (int)post('id');
        $stmt = $pdo->prepare('DELETE FROM sales WHERE id=? AND user_id=?');
        $stmt->execute([$id, $user['id']]);
        header('Location: sales.php');
        exit;
    }
}

// Fetch products and sales
$prod_stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = ?');
$prod_stmt->execute([$user['id']]);
$products = $prod_stmt->fetchAll();

$stmt = $pdo->prepare('SELECT s.*, pr.name as product_name FROM sales s JOIN products pr ON pr.id = s.product_id WHERE s.user_id = ? ORDER BY s.created_at DESC');
$stmt->execute([$user['id']]);
$sales = $stmt->fetchAll();

foreach ($sales as &$s) {
    $latest_purchase = latest_purchase_price($user['id'], $s['product_id']);
    $product_stmt = $pdo->prepare('SELECT pieces_per_pack FROM products WHERE id = ?');
    $product_stmt->execute([$s['product_id']]);
    $product = $product_stmt->fetch();
    $s['profit'] = calculate_profit($s, $product, $latest_purchase);
}
unset($s);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sales - Job Manager</title>
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
      <h2>Record Sale</h2>

      <div class="card">
        <form method="post">
          <label>Product</label>
          <select name="product_id" id="sale_product" required>
            <option value="">Select product</option>
            <?php foreach($products as $p): ?>
              <option value="<?=$p['id']?>" data-pieces="<?=$p['pieces_per_pack']?>"><?=htmlspecialchars($p['name'])?> (<?=$p['pieces_per_pack']?>/pack)</option>
            <?php endforeach; ?>
          </select>
          <label>Unit Type</label>
          <select name="unit_type" id="unit_type">
            <option value="pack">Pack</option>
            <option value="piece">Piece</option>
          </select>
          <label>Quantity</label>
          <input name="quantity" id="quantity" type="number" min="1" value="1" required>
          <label>Sale price (per unit)</label>
          <input name="sale_price" id="sale_price" type="number" step="0.01" required>
          <button name="create_sale">Save Sale</button>
        </form>
      </div>

      <section>
        <h3>Sales History</h3>
        <?php if (empty($sales)): ?>
          <div>No sales recorded.</div>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Time</th><th>Product</th><th>Unit</th><th>Qty</th><th>Sale Price</th><th>Profit</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($sales as $s): ?>
              <tr>
                <td><?=htmlspecialchars($s['created_at'])?></td>
                <td><?=htmlspecialchars($s['product_name'])?></td>
                <td><?=htmlspecialchars($s['unit_type'])?></td>
                <td><?=htmlspecialchars($s['quantity'])?></td>
                <td><?=number_format($s['sale_price'],2)?></td>
                <td><?=number_format($s['profit'],2)?></td>
                <td>
                  <form method="post" onsubmit="return confirm('Delete sale?');">
                    <input type="hidden" name="id" value="<?=$s['id']?>">
                    <button name="delete_sale">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    </main>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
