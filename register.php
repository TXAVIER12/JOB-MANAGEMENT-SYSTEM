<?php
require_once 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$password) $errors[] = 'All fields required.';
    if ($password !== $password2) $errors[] = 'Passwords do not match.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already used.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
            $stmt->execute([$name, $email, $hash]);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - Job Manager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="center-box">
    <h2>Register</h2>
    <?php if (!empty($errors)): ?>
      <div class="errors"><?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <label>Name</label>
      <input type="text" name="name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <label>Confirm Password</label>
      <input type="password" name="password2" required>
      <button type="submit">Register</button>
    </form>
    <p>Back to <a href="index.php">Login</a></p>
  </div>
</body>
</html>
