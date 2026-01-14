<?php
// install.php

// Настройки за връзка (за XAMPP обикновено са тези)
$host = 'localhost';
$user = 'root';
$pass = '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // 1. Свързване към MySQL без да избираме база данни (за да я създадем)
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Създаване на базата данни
        $pdo->exec("CREATE DATABASE IF NOT EXISTS inventory_system");
        $pdo->exec("USE inventory_system");

        // 3. Създаване на таблиците (SQL кодът)
        $sql = "
        -- Таблица Потребители
        CREATE TABLE IF NOT EXISTS users (
            id int(11) NOT NULL AUTO_INCREMENT,
            username varchar(50) NOT NULL,
            password varchar(255) NOT NULL,
            role enum('admin','user') NOT NULL DEFAULT 'user',
            balance decimal(10,2) NOT NULL DEFAULT 2000.00,
            PRIMARY KEY (id),
            UNIQUE KEY username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        -- Таблица Продукти
        CREATE TABLE IF NOT EXISTS products (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text NOT NULL,
            quantity int(11) NOT NULL DEFAULT 0,
            price decimal(10,2) NOT NULL,
            image_url varchar(255) DEFAULT 'https://via.placeholder.com/300x200',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        -- Таблица Поръчки
        CREATE TABLE IF NOT EXISTS orders (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            total_amount decimal(10,2) NOT NULL,
            payment_method varchar(50) NOT NULL,
            courier varchar(50) NOT NULL,
            delivery_address text NOT NULL,
            status varchar(50) DEFAULT 'new',
            created_at datetime DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        -- Таблица Order Items
        CREATE TABLE IF NOT EXISTS order_items (
            id int(11) NOT NULL AUTO_INCREMENT,
            order_id int(11) NOT NULL,
            product_id int(11) NOT NULL,
            quantity int(11) NOT NULL,
            price decimal(10,2) NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        -- Таблица Logs
        CREATE TABLE IF NOT EXISTS stock_logs (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            product_name varchar(255) NOT NULL,
            action_type varchar(50) NOT NULL,
            details varchar(255) DEFAULT NULL,
            log_date datetime DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $pdo->exec($sql);

        // 4. Създаване на АДМИН по подразбиране (ако няма такъв)
        // Парола: admin123
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);

        // Проверка дали има админ
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, balance) VALUES ('admin', ?, 'admin', 0)");
            $stmt->execute([$adminPass]);
            $message = "Базата данни е създадена успешно! Създаден е потребител 'admin' с парола 'admin123'.";
        } else {
            $message = "Базата данни е обновена успешно!";
        }

    } catch (PDOException $e) {
        $message = "Грешка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Инсталация на TechShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-header bg-primary text-white text-center">
            <h3>🚀 Инсталация на Системата</h3>
        </div>
        <div class="card-body text-center">
            <p>Този инструмент автоматично ще създаде базата данни и таблиците.</p>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
                <a href="auth/login.php" class="btn btn-primary w-100">Към Вход</a>
            <?php else: ?>
                <form method="POST">
                    <button type="submit" class="btn btn-success btn-lg w-100">Инсталирай сега</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>