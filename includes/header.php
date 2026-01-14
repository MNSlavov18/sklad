<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_balance = 0;
if (isset($_SESSION['user_id'])) {
    global $pdo;
    if(isset($pdo)) {
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $current_balance = $stmt->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop & Sklad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .product-card:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>index.php"><i class="bi bi-cart4"></i> TechShop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php">Магазин</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($_SESSION['role'] !== 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>client/cart.php">
                                <i class="bi bi-cart-fill"></i> Количка
                                <span class="badge bg-danger rounded-pill">
                                    <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>client/my_orders.php">Моите Поръчки</a></li>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning" href="#" data-bs-toggle="dropdown">Админ Панел</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/dashboard.php">Табло</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/admin_orders.php">Управление Поръчки</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/products_manage.php">Управление Склад</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/history.php">История</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center text-white">
                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($_SESSION['role'] !== 'admin'): ?>
                        <a href="<?= BASE_URL ?>client/profile.php" class="text-decoration-none me-3">
                            <span class="badge bg-success p-2">
                                <i class="bi bi-wallet2"></i> <?= number_format((float)$current_balance, 2) ?> лв.
                                <i class="bi bi-plus-circle-fill ms-1"></i>
                            </span>
                        </a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>client/profile.php" class="text-white text-decoration-none me-3">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>

                    <a href="<?= BASE_URL ?>auth/logout.php" class="btn btn-outline-danger btn-sm">Изход</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-outline-light btn-sm me-2">Вход</a>
                    <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-warning btn-sm">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container flex-grow-1">