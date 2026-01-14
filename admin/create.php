<?php
require '../includes/config.php';
require '../includes/header.php';

// САМО АДМИНИСТРАТОРИ МОГАТ ДА ДОБАВЯТ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger m-5'>Нямате права за тази страница!</div>";
    require 'footer.php';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO products (name, description, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt= $pdo->prepare($sql);

    if ($stmt->execute([$name, $description, $quantity, $price])) {
        // ЗАПИС В ИСТОРИЯТА
        logAction($pdo, $_SESSION['user_id'], $name, 'create', "Добавени $quantity бр. с цена $price");

        echo "<script>window.location='products_manage.php';</script>";
        exit;
    }
}
?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">Добави нов продукт</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Име</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Описание</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label>Количество</label>
                                <input type="number" name="quantity" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label>Цена</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3 w-100">Запиши</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>