<?php
require 'includes/config.php';
require 'includes/header.php';
/** @var PDO $pdo */ //

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) { echo "<script>window.location='auth/login.php';</script>"; exit; }

    // ЗАЩИТА: Ако е админ, спираме действието
    if ($_SESSION['role'] === 'admin') { echo "<script>window.location='index.php';</script>"; exit; }

    $pid = $_POST['product_id'];
    $qty = 1;

    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }

    echo "<script>window.location='index.php';</script>";
    exit;
}

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE quantity > 0 AND name LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC");
}
$products = $stmt->fetchAll();
?>

<div class="text-center mb-5">
    <h1 class="display-4">Добре дошли в TechShop</h1>
    <p class="lead">Качествена техника на добри цени</p>
</div>

<div class="row justify-content-center mb-5">
    <div class="col-md-6">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Търсене на продукт..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary">Търси</button>
        </form>
    </div>
</div>

<div class="row">
    <?php foreach ($products as $product): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <?php
                    $imgUrl = !empty($product['image_url']) ? $product['image_url'] : 'https://via.placeholder.com/300x200?text=Product';
                ?>
                <img src="<?= htmlspecialchars($imgUrl) ?>" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($product['description']) ?></p>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="h5 mb-0 text-primary"><?= number_format($product['price'], 2) ?> лв.</span>
                        <span class="badge bg-success">Налично: <?= $product['quantity'] ?></span>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pb-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] !== 'admin'): ?>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus"></i> Добави в количка
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Админ режим</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-outline-primary w-100">Влез, за да купиш</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (count($products) == 0): ?>
        <div class="col-12 text-center text-muted">Няма намерени продукти.</div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>