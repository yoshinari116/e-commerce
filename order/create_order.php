<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details including brand
    $stmt = $conn->prepare("SELECT pt_name, pt_brand, pt_price FROM products_tbl WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: ../welcome.php?error=Product not found");
        exit;
    }

    $product_name = $product['pt_name'];
    $product_brand = $product['pt_brand'];
    $product_price = $product['pt_price'];

    // Insert the order into order_tbl
    $insertStmt = $conn->prepare("INSERT INTO order_tbl (id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())");
    $insertStmt->execute([$user_id, $product_id, $quantity]);

    header("Location: ../order.php?success=Order placed successfully");
    exit;
} else {
    header("Location: ../welcome.php?error=Invalid request");
    exit;
}
