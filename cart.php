<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product_id'])) {
    $product_id = intval($_POST['remove_product_id']);
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    header('Location: cart.php');
    exit();
}

$stmt = $pdo->prepare("SELECT cart.*, products.name, products.price, products.image FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - E-Commerce Site</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="cart-body">
    <header>
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </header>
    <main class="cart-main">
        <div class="cart-container">
            <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-basket"></i>
                    <h2>Your cart is empty</h2>
                    <p>Add some products to get started!</p>
                    <a href="products.php" class="shop-btn">Shop Now</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="item-price">₹<?php echo number_format($item['price'], 2); ?> each</p>  <!-- Changed to ₹ -->
                                <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                <p class="item-subtotal">Subtotal: ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>  <!-- Changed to ₹ -->
                            </div>
                            <form method="POST" class="remove-form" onsubmit="return confirm('Remove this item?');">
                                <input type="hidden" name="remove_product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="remove-btn"><i class="fas fa-trash"></i> Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <h2>Cart Summary</h2>
                    <p class="total">Total: <span>₹<?php echo number_format($total, 2); ?></span></p>  <!-- Changed to ₹ -->
                    <a href="checkout.php" class="checkout-btn"><i class="fas fa-credit-card"></i> Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/script.js"></script>
</body>
</html>