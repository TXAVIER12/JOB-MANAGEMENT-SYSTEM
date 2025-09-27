<?php
require_once 'config.php';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user) return $user;
    $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

// Safely get POST values
function post($key, $default='') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Calculate profit for a sale record given product and latest purchase price
function calculate_profit($sale, $product, $latest_purchase_price) {
    // $sale: ['unit_type'=>'pack'|'piece', 'quantity'=>int, 'sale_price'=>decimal]
    // $product: ['pieces_per_pack'=>int]
    if ($sale['unit_type'] === 'pack') {
        // Pack profit: (sale_price - purchase_price) * quantity
        $unit_profit = $sale['sale_price'] - $latest_purchase_price;
        return round($unit_profit * $sale['quantity'], 2);
    } else {
        // Piece profit: (sale_price - (purchase_price / pieces_per_pack)) * quantity
        $ppp = max(1, (int)$product['pieces_per_pack']);
        $unit_purchase = $latest_purchase_price / $ppp;
        $unit_profit = $sale['sale_price'] - $unit_purchase;
        return round($unit_profit * $sale['quantity'], 2);
    }
}

// Get latest purchase price for a product for this user (most recent purchase)
function latest_purchase_price($user_id, $product_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT purchase_price FROM purchases WHERE user_id = ? AND product_id = ? ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([$user_id, $product_id]);
    $row = $stmt->fetch();
    return $row ? (float)$row['purchase_price'] : 0.0;
}

