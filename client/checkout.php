<?php
require '../includes/config.php';
require '../includes/header.php';

if (empty($_SESSION['cart'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$saved_address = $_SESSION['checkout_data']['address'] ?? '';
$saved_courier = $_SESSION['checkout_data']['courier'] ?? 'Econt';
$saved_payment = $_SESSION['checkout_data']['payment'] ?? 'cash';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['checkout_data'] = [
        'address' => trim($_POST['address']),
        'courier' => $_POST['courier'],
        'payment' => $_POST['payment']
    ];
    $saved_address = $_SESSION['checkout_data']['address'];
    $saved_courier = $_SESSION['checkout_data']['courier'];
    $saved_payment = $_SESSION['checkout_data']['payment'];

    if (isset($_POST['go_back'])) {
        echo "<script>window.location='cart.php';</script>"; exit;
    }

    if (isset($_POST['place_order'])) {
        $address = $saved_address;
        $courier = $saved_courier;
        $payment = $saved_payment;
        $user_id = $_SESSION['user_id'];

        if(!$address) {
            $error = "Моля, въведете адрес за доставка!";
        } else {
            try {
                $pdo->beginTransaction();

                $ids = array_keys($_SESSION['cart']);
                $in  = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in) FOR UPDATE");
                $stmt->execute($ids);
                $db_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $total = 0;
                $order_items = [];

                foreach ($db_products as $p) {
                    $qty = $_SESSION['cart'][$p['id']];
                    if ($p['quantity'] < $qty) {
                        throw new Exception("Няма достатъчно количество от " . $p['name']);
                    }
                    $total += $p['price'] * $qty;
                    $order_items[] = ['id' => $p['id'], 'qty' => $qty, 'price' => $p['price']];
                }

                if ($payment == 'card') {
                    $uStmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
                    $uStmt->execute([$user_id]);
                    $userBalance = $uStmt->fetchColumn();

                    if ($userBalance < $total) {
                        throw new Exception("Нямате достатъчно средства в картата! Налични: " . number_format($userBalance, 2) . " лв.");
                    }

                    $newBalance = $userBalance - $total;
                    $payStmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                    $payStmt->execute([$newBalance, $user_id]);
                }

                $sql = "INSERT INTO orders (user_id, total_amount, payment_method, courier, delivery_address, status) VALUES (?, ?, ?, ?, ?, 'new')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $total, $payment, $courier, $address]);
                $order_id = $pdo->lastInsertId();

                foreach ($order_items as $item) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['id'], $item['qty'], $item['price']]);

                    $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                    $stmt->execute([$item['qty'], $item['id']]);
                }

                $pdo->commit();
                unset($_SESSION['cart']);
                unset($_SESSION['checkout_data']);

                echo "
                <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Поръчката е приета!',
                            text: 'Успешно плащане! Благодарим Ви.',
                            icon: 'success',
                            confirmButtonColor: '#198754',
                            confirmButtonText: 'Към моите поръчки'
                        }).then((result) => {
                            window.location = 'my_orders.php';
                        });
                    }
                </script>";
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                echo "
                <script>
                    window.onload = function() {
                        Swal.fire({
                            title: 'Грешка!',
                            text: '".$e->getMessage()."',
                            icon: 'error',
                            confirmButtonText: 'Опитай пак'
                        });
                    }
                </script>";
            }
        }
    }
}
?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-center">🏁 Финализиране на поръчката</h2>
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-truck"></i> Данни за доставка и плащане
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">📍 Адрес за доставка</label>
                            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($saved_address) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">🚚 Избери Куриер</label>
                                <div class="list-group">
                                    <label class="list-group-item"><input class="form-check-input me-1" type="radio" name="courier" value="Econt" <?= $saved_courier == 'Econt' ? 'checked' : '' ?>> Еконт</label>
                                    <label class="list-group-item"><input class="form-check-input me-1" type="radio" name="courier" value="Speedy" <?= $saved_courier == 'Speedy' ? 'checked' : '' ?>> Спиди</label>
                                    <label class="list-group-item"><input class="form-check-input me-1" type="radio" name="courier" value="Post" <?= $saved_courier == 'Post' ? 'checked' : '' ?>> Бг Пощи</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">💳 Начин на плащане</label>
                                <div class="list-group">
                                    <label class="list-group-item"><input class="form-check-input me-1" type="radio" name="payment" value="cash" <?= $saved_payment == 'cash' ? 'checked' : '' ?>> Наложен платеж</label>
                                    <label class="list-group-item"><input class="form-check-input me-1" type="radio" name="payment" value="card" <?= $saved_payment == 'card' ? 'checked' : '' ?>> Банкова карта</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="place_order" class="btn btn-success btn-lg">Плати и Поръчай</button>
                            <button type="submit" name="go_back" class="btn btn-outline-secondary">Върни се</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require '../includes/footer.php'; ?>