<?php
session_start();
include 'config.php';

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header><a href="index.php">Home</a></header>
    <main>
        <h2>Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p>₹<?php echo $product['price']; ?></p>  <!-- Changed to ₹ -->
                    <a href="product.php?id=<?php echo $product['id']; ?>"><i class="fas fa-eye"></i> View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>