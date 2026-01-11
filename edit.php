<?php
// edit.php
require 'config.php';
require 'header.php';

// ЗАЩИТА: Само за регистрирани
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

// Взимане на текущите данни
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Продуктът не е намерен!";
    exit;
}

// Обработка на формата
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE products SET name=?, description=?, quantity=?, price=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$name, $description, $quantity, $price, $id]);

    echo "<script>window.location='index.php';</script>";
    exit();
}
?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h3 class="mb-0">Редактирай продукт</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Име на продукта</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Описание</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Количество</label>
                                <input type="number" name="quantity" class="form-control" value="<?= htmlspecialchars($product['quantity']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Цена</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">Обнови</button>
                            <a href="index.php" class="btn btn-secondary">Отказ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require 'footer.php'; ?>