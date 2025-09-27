<?php
require_once 'functions.php';
require_login();
$user = current_user();

// Create, Update, Delete handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_product'])) {
        $name = post('name');
        $pieces = (int)post('pieces_per_pack',1);
        $stmt = $pdo->prepare('INSERT INTO products (user_id,name,pieces_per_pack) VALUES (?,?,?)');
        $stmt->execute([$user['id'], $name, $pieces]);
        header('Location: products.php');
        exit;
    }
    if (isset($_POST['update_product'])) {
        $id = (int)post('id');
        $name = post('name');
        $pieces = (int)post('pieces_per_pack',1);
        $stmt = $pdo->prepare('UPDATE products SET name=?, pieces_per_pack=? WHERE id=? AND user_id=?');
        $stmt->execute([$name, $pieces, $id, $user['id']]);
        header('Location: products.php');
        exit;
    }
    if (isset($_POST['delete_product'])) {
        $id = (int)post('id');
        $stmt = $pdo->prepare('DELETE FROM products WHERE id=? AND user_id=?');
        $stmt->execute([$id, $user['id']]);
        header('Location: products.php');
        exit;
    }
}

// Fetch products for this user
$stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Products - Job Manager</title>
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
      <h2>Products</h2>

      <div class="card">
        <h3>Add Product</h3>
        <form method="post">
          <label>Product Name</label>
          <input name="name" required>
          <label>Pieces per pack</label>
          <input name="pieces_per_pack" type="number" min="1" value="1" required>
          <button name="create_product">Add</button>
        </form>
      </div>

      <section>
        <h3>Your Products</h3>
        <?php if (empty($products)): ?>
          <div>No products yet.</div>
        <?php else: ?>
          <table class="table">
            <thead><tr><th>Name</th><th>Pieces/Pack</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($products as $p): ?>
              <tr>
                <td><?=htmlspecialchars($p['name'])?></td>
                <td><?=htmlspecialchars($p['pieces_per_pack'])?></td>
                <td><?=htmlspecialchars($p['created_at'])?></td>
                <td>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="id" value="<?=$p['id']?>">
                    <input type="hidden" name="name" value="<?=htmlspecialchars($p['name'])?>">
                    <input type="hidden" name="pieces_per_pack" value="<?=$p['pieces_per_pack']?>">
                    <button type="button" onclick="editProduct(<?=htmlspecialchars(json_encode($p))?>)">Edit</button>
                  </form>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete product?');">
                    <input type="hidden" name="id" value="<?=$p['id']?>">
                    <button name="delete_product">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

      <!-- Hidden edit modal -->
      <div id="editModal" class="modal hidden">
        <div class="modal-content">
          <h3>Edit Product</h3>
          <form method="post">
            <input type="hidden" name="id" id="edit_id">
            <label>Name</label>
            <input name="name" id="edit_name" required>
            <label>Pieces per pack</label>
            <input type="number" name="pieces_per_pack" id="edit_pieces" min="1" required>
            <button name="update_product">Update</button>
            <button type="button" onclick="closeModal()">Cancel</button>
          </form>
        </div>
      </div>

    </main>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
