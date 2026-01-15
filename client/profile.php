<?php
require '../includes/config.php';
require '../includes/header.php';

/** @var PDO $pdo */ //

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_funds'])) {
    $amount = (float) $_POST['amount'];

    if ($amount > 0) {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        if ($stmt->execute([$amount, $user_id])) {
            echo "
            <script>
                window.onload = function() {
                    Swal.fire({
                        title: 'Успешно зареждане!',
                        text: 'Добавихте ' + $amount + ' лв. към вашия баланс.',
                        icon: 'success',
                        confirmButtonText: 'Супер'
                    }).then(() => {
                        window.location='profile.php';
                    });
                }
            </script>";
        }
    } else {
        echo "<script>alert('Моля въведете валидна сума!');</script>";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-vcard"></i> Моят Профил</h4>
                </div>
                <div class="card-body">
                    <p><strong>Потребител:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['username']) ?>@example.com</p> <hr>
                    <h5 class="text-success">
                        Наличен Баланс: <span class="fw-bold"><?= number_format($user['balance'], 2) ?> лв.</span>
                    </h5>
                </div>
            </div>

            <?php if($_SESSION['role'] !== 'admin'): ?>
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Зареди Сметка</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Номер на карта</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                    <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Валидна до</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="password" class="form-control" placeholder="123" maxlength="3" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Сума за зареждане (лв.)</label>
                                <input type="number" name="amount" class="form-control form-control-lg" min="10" step="1" placeholder="50" required>
                                <div class="form-text">Минимална сума: 10 лв.</div>
                            </div>

                            <button type="submit" name="add_funds" class="btn btn-success w-100 py-2">
                                <i class="bi bi-plus-circle"></i> Добави средства
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require '../includes/footer.php'; ?>