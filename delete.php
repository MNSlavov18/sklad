<?php
// delete.php
require 'config.php';
session_start(); // Важно! Тук нямаме header.php, затова стартираме сесията ръчно

// ЗАЩИТА: Абсолютно задължителна тук!
// Иначе всеки може да напише delete.php?id=5 и да ти изтрие стоката без да е влязъл.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

// Връщане към началото
header("Location: index.php");
exit;
?>