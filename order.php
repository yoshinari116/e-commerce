<?php
session_start();
include('database/db.php');

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// Fetch orders
$query = "SELECT o.order_id, p.pt_name, p.pt_brand, o.quantity, o.order_date, p.pt_img, p.pt_type
          FROM order_tbl o
          JOIN products_tbl p ON o.product_id = p.product_id
          WHERE o.id = :id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$order_count_query = "SELECT COUNT(*) AS count FROM order_tbl WHERE id = :id";
$order_count_stmt = $conn->prepare($order_count_query);
$order_count_stmt->bindParam(':id', $user_id);
$order_count_stmt->execute();
$order_count_result = $order_count_stmt->fetch(PDO::FETCH_ASSOC);
$order_count = $order_count_result['count'] ?? 0;

$cart_count_query = "SELECT COUNT(*) AS count FROM cart_tbl WHERE user_id = :user_id";
$cart_count_stmt = $conn->prepare($cart_count_query);
$cart_count_stmt->bindParam(':user_id', $user_id);
$cart_count_stmt->execute();
$cart_count_result = $cart_count_stmt->fetch(PDO::FETCH_ASSOC);
$cart_count = $cart_count_result['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
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
            <a class="navbar-brand" href="welcome.php">Home</a>
            <div class="ms-auto">
                <span class="me-3 text-white">Welcome: <?php echo htmlspecialchars($user['username']); ?></span>
                <a href="order.php" class="btn" style="background-color: white; color: #440170;">Orders (<?php echo $order_count; ?>)</a>
                <a href="cart/cart.php" class="btn" style="background-color: white; color: #440170;">
                    Cart (<?php echo $cart_count; ?>)
                </a>
                <a href="php/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Orders Table -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">My Orders</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover order-table">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Product Type</th>
                        <th>Product Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['pt_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['pt_brand']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['pt_type']); ?></td>
                                <td>
                                    <img src="product/product_img/<?php echo htmlspecialchars($order['pt_img']); ?>" alt="Product Image" class="img-fluid" style="max-width: 100px;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
