<?php
session_start();
include 'config.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->execute([$user_id, $id, $quantity, $quantity]);
    header('Location: cart.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header><a href="products.php">Back to Products</a></header>
    <main>
        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        <h2><?php echo $product['name']; ?></h2>
        <p><?php echo $product['description']; ?></p>
        <p>₹<?php echo $product['price']; ?></p>  <!-- Changed to ₹ -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit">Add to Cart</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login to add to cart</a></p>
        <?php endif; ?>
    </main>
</body>
</html>