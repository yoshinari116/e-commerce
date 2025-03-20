<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['cart_id']) || !is_numeric($_GET['cart_id'])) {
    $_SESSION['message'] = "Invalid cart item!";
    header("Location: cart.php");
    exit;
}

$cart_id = $_GET['cart_id'];
$user_id = $_SESSION['user']['id'];

$query = "DELETE FROM cart_tbl WHERE cart_id = :cart_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if ($stmt->execute() && $stmt->rowCount() > 0) {
    $_SESSION['message'] = "Item removed successfully!";
} else {
    $_SESSION['message'] = "Failed to remove item or item not found!";
}

header("Location: cart.php");
exit;
?>
