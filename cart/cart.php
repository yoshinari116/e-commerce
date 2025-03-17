<?php
session_start();
include('../database/db.php');

if (!isset($_SESSION['user'])) {
    header('Location: ../index.php'); 
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

$query = "SELECT c.cart_id, p.pt_name, c.quantity, p.pt_img, p.pt_type, p.pt_price, c.product_id
          FROM cart_tbl c
          JOIN products_tbl p ON c.product_id = p.product_id
          WHERE c.user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>

<div class="navbar" style="background-color: #00247E;">
    <a href="../welcome.php">Home</a>
    <span class="username">Welcome: <?php echo htmlspecialchars($user['username']); ?>!</span>
    <a href="../php/logout.php">Logout</a>
</div>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Cart</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Product Type</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['pt_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['pt_type']); ?></td>
                            <td>$<?php echo number_format($item['pt_price'], 2); ?></td>
                            <td>
                                <img src="../product/product_img/<?php echo htmlspecialchars($item['pt_img']); ?>" alt="Product Image" class="img-fluid" style="max-width: 100px;">
                            </td>
                            <td>
                                <a href="cart/cart_remove.php?cart_id=<?php echo $item['cart_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                                
                                <form action="../order/create_order.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Place Order</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">_</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <a href="../welcome.php" class="btn btn-primary" style="background-color: #00247E; color: white;">Return to Shopping</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
