<?php
// Edit these values to match your MySQL setup
$db_host = '127.0.0.1';
$db_name = 'job_manager';
$db_user = 'root';
$db_pass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
session_start();
