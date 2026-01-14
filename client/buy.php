<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $qty_to_buy = 1;

    // Примерни данни за директна покупка (тъй като няма форма за адрес)
    $payment = 'cash';
    $courier = 'Econt';
    $address = 'Адрес по подразбиране (от профила)';

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT price, quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $product['quantity'] >= $qty_to_buy) {
            $total = $product['price'] * $qty_to_buy;

            $sql = "INSERT INTO orders (user_id, total_amount, payment_method, courier, delivery_address) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $total, $payment, $courier, $address]);
            $order_id = $pdo->lastInsertId();

            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($itemSql);
            $stmt->execute([$order_id, $product_id, $qty_to_buy, $product['price']]);

            $upd = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $upd->execute([$qty_to_buy, $product_id]);

            $pdo->commit();
            header("Location: my_orders.php?status=success");
            exit;
        } else {
            throw new Exception("Няма наличност!");
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Грешка: " . $e->getMessage());
    }
}
?>