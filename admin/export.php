<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) { exit; }

// Настройки за сваляне на файл
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sklad_nalichnosti_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// BOM за кирилица в Excel
fputs($output, $bom =( export . phpchr(0xEF) . chr(0xBB) . chr(0xBF) ));

// Заглавия на колоните
fputcsv($output, ['ID', 'Име на продукт', 'Описание', 'Количество', 'Ед. Цена', 'Обща Стойност']);

// Данни
$stmt = $pdo->query("SELECT id, name, description, quantity, price FROM products");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total = $row['quantity'] * $row['price'];
    // Добавяме общата стойност към реда
    $row['total'] = $total;
    fputcsv($output, $row);
}
fclose($output);
exit;
?>