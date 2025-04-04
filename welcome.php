<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $login_user = $_SESSION['user'];
} else {
    header('Location: index.php');
    exit;
}

$cart_query = "SELECT COUNT(*) AS cart_count FROM cart_tbl WHERE user_id = :user_id";
$cart_stmt = $conn->prepare($cart_query);
$cart_stmt->bindParam(':user_id', $login_user['id']);
$cart_stmt->execute();
$cart_count = $cart_stmt->fetch(PDO::FETCH_ASSOC)['cart_count'] ?? 0;

$order_query = "SELECT COUNT(*) AS order_count FROM order_tbl WHERE id = :user_id";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bindParam(':user_id', $login_user['id']);
$order_stmt->execute();
$order_count = $order_stmt->fetch(PDO::FETCH_ASSOC)['order_count'] ?? 0;

$product_query = "SELECT * FROM products_tbl";
$product_stmt = $conn->query($product_query);
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/welcome.css">
    <style>
        .navbar {
            background-color: #440170;
            font-weight: bold;
            font-size: 18px;    
        }
        .navbar .username {
            color: white;
        }
        .product-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .btn.order {
            background-color: #440170;
            color: white;
        }
        .btn.order:hover {
            background-color: #440170;
        }
        .btn.cart {
            background-color: #7800b8;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">E-Commerce</a>
            <div class="ms-auto">
                <span class="me-3 text-white">Welcome: <?php echo $login_user['username']; ?></span>
                <a href="order.php" class="btn" style="background-color: white; color: #440170;">Orders (<?php echo $order_count; ?>)</a>
                <a href="cart/cart.php" class="btn" style="background-color: white; color: #440170;">Cart (<?php echo $cart_count; ?>)</a>
                <a href="php/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <?php foreach ($products as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <img src="product/product_img/<?php echo htmlspecialchars($row['pt_img']); ?>" class="card-img-top product-image" alt="Product Image">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['pt_name']); ?></h5>
                            <p class="card-text">Brand: <?php echo htmlspecialchars($row['pt_brand']); ?></p>
                            <p class="card-text">Type: <?php echo htmlspecialchars($row['pt_type']); ?></p>
                            <p class="card-text text-primary fw-bold">Price: ₱<?php echo number_format($row['pt_price'], 2); ?></p>

                            <form action="" method="POST" class="d-grid">
                                <label class="form-label">Quantity</label>
                                <input name="quantity" required type="number" min="1" class="form-control mb-2">
                                
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $login_user['id']; ?>">

                                <button type="submit" formaction="cart/add_to_cart.php" class="btn cart mb-2">Add to Cart</button>
                                <button type="submit" formaction="order/create_order.php" class="btn order">Order Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
