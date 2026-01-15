<?php
require '../includes/config.php';
/** @var PDO $pdo */ //

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php"); exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if ($prod) {
        $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$id]);

        logAction($pdo, $_SESSION['user_id'], $prod['name'], 'delete', "Продуктът беше изтрит");
    }
}
header("Location: products_manage.php");