<?php
// create.php
require 'config.php';
require 'header.php'; // Включва сесията и дизайна

// ЗАЩИТА: Ако потребителят не е логнат, го пращаме да се логне
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO products (name, description, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);

    if ($stmt->execute([$name, $description, $quantity, $price])) {
        // Успех -> връщаме се в началото
        echo "<script>window.location='index.php';</script>";
        exit;
    } else {
        $message = "Грешка при запис!";
    }
}
?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Добави нов продукт</h3>
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-danger"><?= $message ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Име на продукта</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Описание</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Количество</label>
                                <input type="number" name="quantity" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Цена (лв.)</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Запиши</button>
                            <a href="index.php" class="btn btn-secondary">Отказ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require 'footer.php'; ?>