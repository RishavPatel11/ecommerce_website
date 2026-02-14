<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT cart.*, products.name, products.price, products.image FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$message = '';
$order_placed = false;
$payment_method = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['place_order']) && !empty($cart_items)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->execute([$user_id, $total]);
            $order_id = $pdo->lastInsertId();

            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            }

            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
            $pdo->commit();
            $order_placed = true;
            $message = 'Order placed successfully! Please select a payment method.';
            $cart_items = [];
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = 'Error placing order. Please try again.';
        }
    } elseif (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
        if ($payment_method == 'cod') {
            $message = 'Payment selected: Cash on Delivery. Your order will be delivered soon!';
        } elseif ($payment_method == 'qr') {
            $message = 'Payment selected: QR Code. Scan the code below to pay.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - E-Commerce Site</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="checkout-body">
    <header>
        <a href="cart.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Cart</a>
    </header>
    <main class="checkout-main">
        <div class="checkout-container">
            <h1><i class="fas fa-credit-card"></i> Checkout</h1>
            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'successfully') !== false || strpos($message, 'selected') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <?php if (!$order_placed && !empty($cart_items)): ?>
                <div class="order-summary">
                    <h2><i class="fas fa-list"></i> Order Summary</h2>
                    <div class="summary-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="summary-image">
                                <div class="summary-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>₹<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?> = ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>  <!-- Changed to ₹ -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="total-section">
                        <p class="total">Total: <span>₹<?php echo number_format($total, 2); ?></span></p>  <!-- Changed to ₹ -->
                    </div>
                </div>
                <form method="POST" class="checkout-form">
                    <button type="submit" name="place_order" class="place-order-btn"><i class="fas fa-check-circle"></i> Place Order</button>
                </form>
            <?php elseif ($order_placed && !$payment_method): ?>
                <div class="payment-options">
                    <h2><i class="fas fa-money-bill-wave"></i> Select Payment Method</h2>
                    <form method="POST" class="payment-form">
                        <div class="payment-choice">
                            <input type="radio" id="cod" name="payment_method" value="cod" required>
                            <label for="cod"><i class="fas fa-hand-holding-usd"></i> Cash on Delivery (COD)</label>
                        </div>
                        <div class="payment-choice">
                            <input type="radio" id="qr" name="payment_method" value="qr" required>
                            <label for="qr"><i class="fas fa-qrcode"></i> Pay via QR Code</label>
                        </div>
                        <button type="submit" class="confirm-payment-btn"><i class="fas fa-check"></i> Confirm Payment</button>
                    </form>
                </div>
            <?php elseif ($payment_method == 'qr'): ?>
                <div class="qr-payment">
                    <h2><i class="fas fa-qrcode"></i> Pay via QR Code</h2>
                    <p>Scan the QR code below with your payment app to complete the payment of <strong>₹<?php echo number_format($total, 2); ?></strong>.</p>  <!-- Changed to ₹ -->
                    <?php
                    require_once('phpqrcode/qrlib.php');
                    $qr_content = "Pay ₹" . number_format($total, 2) . " for Order ID: " . ($order_id ?? 'N/A');  // Changed to ₹
                    $qr_file = 'images/order_qr_' . session_id() . '.png';
                    QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 10);
                    ?>
                    <img src="<?php echo $qr_file; ?>" alt="QR Code for Payment" class="qr-code">
                    <p>After payment, your order will be processed.</p>
                    <a href="index.php" class="continue-btn">Back to Home</a>
                </div>
            <?php elseif ($payment_method == 'cod'): ?>
                <div class="cod-confirmation">
                    <h2><i class="fas fa-check-circle"></i> Order Confirmed</h2>
                    <p>You selected Cash on Delivery. Pay when your order arrives.</p>
                    <a href="index.php" class="continue-btn">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/script.js"></script>
</body>
</html>