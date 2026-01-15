<?php
require '../includes/config.php';
require '../includes/header.php';
/** @var PDO $pdo */ //

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Взимаме логовете + името на потребителя
$sql = "SELECT logs.*, users.username 
        FROM stock_logs AS logs 
        JOIN users ON logs.user_id = users.id 
        ORDER BY logs.log_date DESC";
$stmt = $pdo->query($sql);
$logs = $stmt->fetchAll();
?>

    <h2 class="mb-4"><i class="bi bi-clock-history"></i> История на действията</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Дата</th>
                    <th>Потребител</th>
                    <th>Действие</th>
                    <th>Продукт</th>
                    <th>Детайли</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['log_date'] ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($log['username']) ?></span></td>
                        <td>
                            <?php
                            $badges = [
                                'create' => ['bg-success', 'Създаване'],
                                'update' => ['bg-warning text-dark', 'Редакция'],
                                'delete' => ['bg-danger', 'Изтриване']
                            ];
                            $type = $log['action_type'];
                            $class = $badges[$type][0] ?? 'bg-light text-dark';
                            $label = $badges[$type][1] ?? $type;
                            ?>
                            <span class="badge <?= $class ?>"><?= $label ?></span>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($log['product_name']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($log['details']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>