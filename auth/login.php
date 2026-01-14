<?php
require '../includes/config.php';
require '../includes/header.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Запазване на ролята в сесията

        // Логика за бисквитка (30 дни)
        if ($remember) {
            setcookie('remember_user', $user['id'], time() + (86400 * 30), "/");
        }

        if ($user['role'] === 'admin') {
            echo "<script>window.location='" . BASE_URL . "admin/dashboard.php';</script>";
        } else {
            echo "<script>window.location='" . BASE_URL . "index.php';</script>";
        }
        exit;
    } else {
        $error = "Грешно име или парола!";
    }
}
?>

    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">🔐 Вход в системата</div>
                <div class="card-body">
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Потребител</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Парола</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Запомни ме</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Вход</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="register.php" class="text-decoration-none">Нямаш акаунт? Регистрирай се</a>
                </div>
            </div>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>