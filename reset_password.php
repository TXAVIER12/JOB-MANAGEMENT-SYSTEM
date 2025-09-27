<?php
require_once 'functions.php';
require_login();
$user = current_user();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $new2 = $_POST['new_password2'] ?? '';
    if (!$current || !$new) $errors[] = 'All fields required.';
    if ($new !== $new2) $errors[] = 'New passwords do not match.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $row = $stmt->fetch();
        if (!$row || !password_verify($current, $row['password'])) {
            $errors[] = 'Current password incorrect.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE users SET password=? WHERE id=?');
            $update->execute([$hash, $user['id']]);
            $success = 'Password updated successfully.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reset Password - Job Manager</title>
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
      <h2>Reset Password</h2>
      <?php if ($success): ?><div class="success"><?=htmlspecialchars($success)?></div><?php endif; ?>
      <?php if (!empty($errors)): ?><div class="errors"><?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?></div><?php endif; ?>
      <form method="post">
        <label>Current password</label>
        <input type="password" name="current_password" required>
        <label>New password</label>
        <input type="password" name="new_password" required>
        <label>Confirm new password</label>
        <input type="password" name="new_password2" required>
        <button>Update Password</button>
      </form>
    </main>
  </div>
</body>
</html>
