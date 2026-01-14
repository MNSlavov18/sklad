<?php
require '../includes/config.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='".BASE_URL."index.php';</script>"; exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $order_id = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    echo "<script>
        Swal.fire({
            title: 'Обновено!',
            text: 'Статусът беше променен успешно.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location='admin_orders.php';
        });
    </script>";
}

$sql = "SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll();

$statuses = [
    'new' => 'Нова',
    'processing' => 'Обработва се',
    'sent' => 'Изпратена',
    'completed' => 'Доставена',
    'cancelled' => 'Отказана'
];
?>

    <h2 class="mb-4">📦 Управление на Поръчки</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Детайли</th>
                    <th>Сума</th>
                    <th>Статус (Промяна)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($order['username']) ?></strong><br>
                            <small class="text-muted"><?= $order['created_at'] ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($order['delivery_address']) ?><br>
                            <span class="badge bg-secondary"><?= $order['courier'] ?></span>
                            <span class="badge bg-info text-dark"><?= $order['payment_method'] == 'card' ? 'Карта' : 'Наложен' ?></span>
                        </td>
                        <td class="fw-bold"><?= $order['total_amount'] ?> лв.</td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                    <?php foreach($statuses as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $order['status'] == $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>