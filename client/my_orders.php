<?php
require '../includes/config.php';
require '../includes/header.php';
/** @var PDO $pdo */ //

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$statusLabels = [
        'new' => ['Нова', 'bg-primary'],
        'processing' => ['Обработва се', 'bg-warning text-dark'],
        'sent' => ['Изпратена', 'bg-info text-dark'],
        'completed' => ['Доставена', 'bg-success'],
        'cancelled' => ['Отказана', 'bg-danger']
];
?>

    <div class="container mt-4">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <script>
                Swal.fire({
                    title: 'Поръчката е приета!',
                    text: 'Успешно плащане! Благодарим Ви.',
                    icon: 'success',
                    confirmButtonColor: '#198754'
                });
            </script>
        <?php endif; ?>

        <h2>📦 Моите поръчки</h2>
        <div class="card shadow mt-3">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Дата</th>
                        <th>Сума</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-bold">#<?= $order['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= number_format($order['total_amount'], 2) ?> лв.</td>
                            <td>
                                <?php
                                $s = $order['status'];
                                $label = $statusLabels[$s][0] ?? $s;
                                $class = $statusLabels[$s][1] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $class ?>"><?= $label ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(count($orders) == 0): ?>
                    <p class="text-center mt-3">Нямате поръчки. <a href="../index.php">Към магазина</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>