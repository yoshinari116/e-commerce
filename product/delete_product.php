<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch product image to delete from folder
    $query = "SELECT pt_img FROM products_tbl WHERE product_id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $imagePath = "../product/product_img/" . $product['pt_img'];
        if (file_exists($imagePath) && !empty($product['pt_img'])) {
            unlink($imagePath); // Delete image file
        }

        // Delete product from database
        $query = "DELETE FROM products_tbl WHERE product_id=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['successMessage'] = "Product deleted successfully!";
        } else {
            $_SESSION['errorMessage'] = "Failed to delete product.";
        }
    }
}

header('Location: ../products_page.php');
exit;
?>
