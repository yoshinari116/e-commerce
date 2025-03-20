<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

    if (!$user_id || !$product_id || !$quantity || $quantity < 1) {
        header("Location: ../welcome.php?error=invalid_input");
        exit;
    }

    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT quantity FROM cart_tbl WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingItem) {
        // If item exists, update quantity
        $newQuantity = $existingItem['quantity'] + $quantity;
        $updateStmt = $conn->prepare("UPDATE cart_tbl SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $updateStmt->execute([$newQuantity, $user_id, $product_id]);
    } else {
        // Insert new item
        $insertStmt = $conn->prepare("INSERT INTO cart_tbl (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->execute([$user_id, $product_id, $quantity]);
    }

    // Redirect with success message
    header("Location: ../welcome.php?cart_success=true");
    exit;
} else {
    header("Location: ../welcome.php?error=invalid_request");
    exit;
}
