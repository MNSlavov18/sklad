<?php
// products_manage.php
require '../includes/config.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Наличности</h2>
        <div>
            <a href="export.php" class="btn btn-outline-success me-2"><i class="bi bi-file-earmark-excel"></i> Експорт Excel</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Нов продукт</a>
            <?php endif; ?>
        </div>
    </div>

    <form class="row g-3 mb-4" method="GET">
        <div class="col-auto flex-grow-1">
            <input type="text" name="search" class="form-control" placeholder="Търси по име..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Търси</button>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                <tr>
                    <th>Продукт</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Общо</th>
                    <?php if ($_SESSION['role'] === 'admin'): ?><th>Действия</th><?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($product['description']) ?></small>
                        </td>
                        <td>
                            <?php if($product['quantity'] < 5): ?>
                                <span class="badge bg-danger"><?= $product['quantity'] ?> бр.</span>
                            <?php else: ?>
                                <span class="badge bg-success"><?= $product['quantity'] ?> бр.</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($product['price'], 2) ?> лв.</td>
                        <td class="fw-bold text-primary"><?= number_format($product['price'] * $product['quantity'], 2) ?> лв.</td>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <td>
                                <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Сигурни ли сте?')"><i class="bi bi-trash"></i></a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require '../includes/footer.php'; ?>