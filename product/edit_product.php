<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = trim($_POST['product_id']);
    $name = trim($_POST['product_name']);
    $brand = trim($_POST['product_brand']);
    $type = trim($_POST['product_type']);
    $price = trim($_POST['product_price']);

    $query = "SELECT pt_img FROM products_tbl WHERE product_id=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldImage = $product['pt_img'];

    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $uploadDir = "../product/product_img/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newImageName = $_FILES['image']['name'];
        $uploadPath = $uploadDir . $newImageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            if (!empty($oldImage) && file_exists($uploadDir . $oldImage) && $oldImage !== $newImageName) {
                unlink($uploadDir . $oldImage);
            }

            $query = "UPDATE products_tbl SET pt_name=?, pt_brand=?, pt_type=?, pt_price=?, pt_img=? WHERE product_id=?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$name, $brand, $type, $price, $newImageName, $id]);
        } else {
            $_SESSION['errorMessage'] = "Image upload failed.";
            header('Location: ../products_page.php');
            exit;
        }
    } else {
        $query = "UPDATE products_tbl SET pt_name=?, pt_brand=?, pt_type=?, pt_price=? WHERE product_id=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$name, $brand, $type, $price, $id]);
    }

    $_SESSION['successMessage'] = "Product updated successfully!";
    header('Location: ../products_page.php');
    exit;
}
?>
