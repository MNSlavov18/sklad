<?php
require '../includes/config.php';
require '../includes/header.php';

/** @var PDO $pdo */ //

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php"); exit;
}

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $qty = $_POST['quantity'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $sql = "UPDATE products SET name=?, description=?, quantity=?, price=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$name, $desc, $qty, $price, $id]);

    // ЛОГВАНЕ
    logAction($pdo, $_SESSION['user_id'], $name, 'update', "Обновено количество на: $qty");

    echo "<script>window.location='products_manage.php';</script>";
    exit;
}
?>
    <div class="container mt-4">
        <h3>Редактирай <?= htmlspecialchars($product['name']) ?></h3>
        <form method="POST">
            <input type="text" name="name" class="form-control mb-2" value="<?= $product['name'] ?>" required>
            <textarea name="description" class="form-control mb-2"><?= $product['description'] ?></textarea>
            <input type="number" name="quantity" class="form-control mb-2" value="<?= $product['quantity'] ?>" required>
            <input type="number" step="0.01" name="price" class="form-control mb-2" value="<?= $product['price'] ?>" required>
            <button class="btn btn-warning">Обнови</button>
        </form>
    </div>
<?php require '../includes/footer.php'; ?>