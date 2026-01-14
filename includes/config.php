<?php
// includes/config.php
$host = 'localhost';
$db   = 'inventory_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

define('BASE_URL', 'http://localhost/sklad/');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function logAction($pdo, $userId, $prodName, $action, $details = '') {
    $sql = "INSERT INTO stock_logs (user_id, product_name, action_type, details) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $prodName, $action, $details]);
}
?>