<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

$message = ''; // For success/error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $image_name = time() . '_' . basename($_FILES['image']['name']); // Unique filename
            $target_path = 'images/' . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image = $image_name;
            } else {
                $message = 'Error uploading image.';
            }
        } else {
            $message = 'Invalid image type or size (max 2MB, JPEG/PNG/GIF only).';
        }
    }
    
    if ($name && $price > 0 && $image) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $image])) {
            $message = 'Product added successfully!';
        } else {
            $message = 'Error adding product.';
        }
    } elseif (!$image) {
        $message = 'Please upload an image.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header><a href="index.php">Home</a></header>
    <main>
        <h2>Add Product</h2>
        <?php if ($message): ?>
            <p style="color: <?php echo strpos($message, 'Error') === false ? 'green' : 'red'; ?>;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Add Product</button>
        </form>
    </main>
</body>
</html>