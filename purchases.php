<?php
require_once 'functions.php';
require_login();
$user = current_user();

// Create purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_purchase'])) {
        $product_id = (int)post('product_id');
        $packs = (int)post('packs');
        $purchase_price = (float)post('purchase_price');
        $stmt = $pdo->prepare('INSERT INTO purchases (user_id, product_id, packs, purchase_price) VALUES (?,?,?,?)');
        $stmt->execute([$user['id'], $product_id, $packs, $purchase_price]);
        header('Location: purchases.php');
        exit;
    }
    if (isset($_POST['delete_purchase'])) {
        $id = (int)post('id');
        $stmt = $pdo->prepare('DELETE FROM purchases WHERE id=? AND user_id=?');
        $stmt->execute([$id, $user['id']]);
        header('Location: purchases.php');
        exit;
    }
}

// Get user's products and purchases
$prod_stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = ?');
$prod_stmt->execute([$user['id']]);
$products = $prod_stmt->fetchAll();

$stmt = $pdo->prepare('SELECT pu.*, pr.name FROM purchases pu JOIN products pr ON pr.id = pu.product_id WHERE pu.user_id = ? ORDER BY pu.created_at DESC');
$stmt->execute([$user['id']]);
$purchases = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Purchases - Job Manager</title>
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
      <h2>Record Purchase</h2>
      <div class="card">
        <form method="post">
          <label>Product</label>
          <select name="product_id" required>
            <option value="">Select product</option>
            <?php foreach($products as $p): ?>
              <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?>(<?=$p['pieces_per_pack']?> per pack)</option>
            <?php endforeach; ?>
          </select>
          <label>Packs bought</label>
          <input name="packs" type="number" min="1" value="1" required>
          <label>Purchase price (per pack)</label>
          <input name="purchase_price" type="number" step="0.01" required>
          <button name="create_purchase">Save Purchase</button>
        </form>
      </div>

      <section>
        <h3>Your Purchases</h3>
        <?php if (empty($purchases)): ?>
          <div>No purchases recorded.</div>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Time</th><th>Product</th><th>Packs</th><th>Price per pack</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($purchases as $pu): ?>
              <tr>
                <td><?=htmlspecialchars($pu['created_at'])?></td>
                <td><?=htmlspecialchars($pu['name'])?></td>
                <td><?=htmlspecialchars($pu['packs'])?></td>
                <td><?=number_format($pu['purchase_price'],2)?></td>
                <td>
                  <form method="post" onsubmit="return confirm('Delete purchase?');" style="display:inline">
                    <input type="hidden" name="id" value="<?=$pu['id']?>">
                    <button name="delete_purchase">Delete</button>
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
</body>
</html>
