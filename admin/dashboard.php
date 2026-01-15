<?php
require '../includes/config.php';
require '../includes/header.php';

/** @var PDO $pdo */ //

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Ако не е админ, го пращаме в магазина
    echo "<script>window.location='index.php';</script>";
    exit;
}

$totalSales = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity < 5")->fetchColumn();
?>

    <h2 class="mb-4">👨‍💼 Админ Панел</h2>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h3><?= number_format($totalSales, 2) ?> лв.</h3>
                    <p>Общ оборот от продажби</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h3><?= $totalOrders ?></h3>
                    <p>Брой поръчки</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <h3><?= $lowStock ?></h3>
                    <p>Стоки на изчерпване</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <a href="products_manage.php" class="btn btn-outline-dark w-100 p-4">
                <i class="bi bi-box-seam fs-1"></i><br>Управление на Склад
            </a>
        </div>
        <div class="col-md-6">
            <a href="history.php" class="btn btn-outline-dark w-100 p-4">
                <i class="bi bi-clock-history fs-1"></i><br>История на действията
            </a>
        </div>
    </div>

<?php require '../includes/footer.php'; ?>