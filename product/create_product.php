<?php

session_start();
include('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name  = trim($_POST['product_name']);
    $brand = trim($_POST['product_brand']);
    $type  = trim($_POST['product_type']);
    $price = trim($_POST['product_price']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgName   = $_FILES['image']['name'];
        $imgTmp    = $_FILES['image']['tmp_name'];
        $uploadDir = 'product_img/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadPath = $uploadDir . $imgName;

        if (!move_uploaded_file($imgTmp, $uploadPath)) {
            $_SESSION['errorMessage'] = "Error uploading the image.";
            header("Location: ../products_page.php");
            exit;
        }
    } else {
        $imgName = null;
    }

    $stmt = $conn->prepare("INSERT INTO products_tbl (pt_name, pt_brand, pt_type, pt_price, pt_img) 
                            VALUES (:name, :brand, :type, :price, :image)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':brand', $brand);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $imgName);
    $stmt->execute();

    $_SESSION['successMessage'] = "Product added successfully!";
    
    header("Location: ../products_page.php");
    exit;
}

header("Location: ../products_page.php");
exit;
?>
