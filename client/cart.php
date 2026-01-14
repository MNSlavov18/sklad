<?php
require '../includes/config.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='../auth/login.php';</script>";
    exit;
}

if ($_SESSION['role'] === 'admin') {
    echo "<script>window.location='../index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $pid => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid] = (int)$quantity;
        }
    }
    echo "<script>window.location='cart.php';</script>";
}

if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    echo "<script>window.location='cart.php';</script>";
}

$products_in_cart = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    if (!empty($ids)) {
        $in  = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        $products_in_cart = $stmt->fetchAll();
    }
}
?>

<div class="container mt-4">
    <h2>🛒 Вашата количка</h2>

    <?php if (empty($products_in_cart)): ?>
        <div class="alert alert-info">
            Количката е празна. <a href="<?= BASE_URL ?>index.php">Към магазина</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="update_cart" value="1">
            <table class="table table-bordered bg-white align-middle">
                <thead><tr><th>Продукт</th><th>Цена</th><th>Количество</th><th>Общо</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($products_in_cart as $p):
                        $qty = $_SESSION['cart'][$p['id']];
                        $subtotal = $p['price'] * $qty;
                        $total_price += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['price'], 2) ?> лв.</td>
                        <td><input type="number" name="qty[<?= $p['id'] ?>]" class="form-control text-center" value="<?= $qty ?>" min="1" max="<?= $p['quantity'] ?>" onchange="this.form.submit()"></td>
                        <td><?= number_format($subtotal, 2) ?> лв.</td>
                        <td><a href="cart.php?remove=<?= $p['id'] ?>" class="btn btn-sm btn-danger">X</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr><td colspan="3" class="text-end fw-bold">ОБЩО:</td><td colspan="2" class="fw-bold text-success fs-4"><?= number_format($total_price, 2) ?> лв.</td></tr>
                </tbody>
            </table>
            <div class="text-end"><a href="checkout.php" class="btn btn-success btn-lg">Продължи към Поръчка →</a></div>
        </form>
    <?php endif; ?>
</div>
<?php require '../includes/footer.php'; ?>