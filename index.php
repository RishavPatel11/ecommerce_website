<?php
session_start();
include 'config.php';

// Fetch featured products (limit to 6 for display)
$stmt = $pdo->query("SELECT * FROM products LIMIT 6");
$featured_products = $stmt->fetchAll();

// Get user greeting if logged in
$greeting = "Welcome to Our Store!";
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $greeting = "Welcome back, " . htmlspecialchars($user['username']) . "!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Commerce Site</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><?php echo $greeting; ?></h1>
        <nav>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout</a>
                <?php if ($_SESSION['is_admin']): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h2>Discover Amazing Electronics</h2>
            <p>Explore our latest collection of mobiles, TVs, remotes, headphones, speakers, and more! Shop now and enjoy exclusive deals.</p>
            <a href="products.php" class="cta-button">Shop Now</a>
        </section>
        
        
    </main>
    <script src="js/script.js"></script>
</body>
</html>