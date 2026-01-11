<?php
require 'config.php';
require 'header.php'; // Включваме менюто

// Логика за търсене
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
        <h1>📦 Складови наличности</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Добави стока</a>
        <?php endif; ?>
    </div>

    <form class="row g-3 mb-4" method="GET">
        <div class="col-auto">
            <input type="text" name="search" class="form-control" placeholder="Търси продукт..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Търси</button>
            <?php if($search): ?>
                <a href="index.php" class="btn btn-secondary">Изчисти</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Продукт</th>
                    <th>Описание</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <th>Действия</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?= $product['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td>
                                <?php if($product['quantity'] < 5): ?>
                                    <span class="badge bg-danger">Изчерпване (<?= $product['quantity'] ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $product['quantity'] ?> бр.</span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($product['price'], 2) ?> лв.</td>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <td>
                                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Сигурни ли сте?')"><i class="bi bi-trash"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">Няма намерени продукти.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require 'footer.php'; ?>