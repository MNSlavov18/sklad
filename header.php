<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Складова Система</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-box-seam"></i> СкладПро
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Начало</a></li>
            </ul>

            <div class="d-flex flex-column flex-lg-row align-items-lg-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-white me-3 mb-2 mb-lg-0">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </span>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Изход</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm me-2 mb-2 mb-lg-0">Вход</a>
                    <a href="register.php" class="btn btn-warning btn-sm">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container flex-grow-1"> <script src...`):

                                                                             ```php
</div> <footer class="text-center mt-5 py-3 text-muted border-top">
    &copy; <?= date('Y') ?> Моята Складова Система. Всички права запазени.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>