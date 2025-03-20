<?php
session_start();
require 'database/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT role, COUNT(*) AS count FROM users GROUP BY role");
$stmt->execute();
$user_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT pt_type, COUNT(*) AS count FROM products_tbl GROUP BY pt_type");
$stmt->execute();
$product_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT users.username, COUNT(*) AS count FROM order_tbl JOIN users ON order_tbl.id = users.id GROUP BY users.username");
$stmt->execute();
$orders_per_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_types_json = json_encode($user_types);
$product_categories_json = json_encode($product_categories);
$orders_per_user_json = json_encode($orders_per_user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/analytics.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            let userTypes = <?php echo $user_types_json; ?>;
            let productCategories = <?php echo $product_categories_json; ?>;
            let ordersPerUser = <?php echo $orders_per_user_json; ?>;

            let userTypesData = new google.visualization.DataTable();
            userTypesData.addColumn('string', 'User Role');
            userTypesData.addColumn('number', 'Count');
            userTypes.forEach(row => userTypesData.addRow([row.role, parseInt(row.count)]));

            let userTypesChart = new google.visualization.PieChart(document.getElementById('userTypesChart'));
            userTypesChart.draw(userTypesData, {title: 'User Role Distribution'});

            let productCategoriesData = new google.visualization.DataTable();
            productCategoriesData.addColumn('string', 'Category');
            productCategoriesData.addColumn('number', 'Count');
            productCategories.forEach(row => productCategoriesData.addRow([row.pt_type, parseInt(row.count)]));

            let productCategoriesChart = new google.visualization.PieChart(document.getElementById('productCategoriesChart'));
            productCategoriesChart.draw(productCategoriesData, {title: 'Product Categories'});

            let ordersPerUserData = new google.visualization.DataTable();
            ordersPerUserData.addColumn('string', 'User');
            ordersPerUserData.addColumn('number', 'Orders');
            ordersPerUser.forEach(row => ordersPerUserData.addRow([row.username, parseInt(row.count)]));

            let ordersPerUserChart = new google.visualization.PieChart(document.getElementById('ordersPerUserChart'));
            ordersPerUserChart.draw(ordersPerUserData, {title: 'Orders Per User'});
        }
    </script>
</head>
<body>

    <div class="navbar">
        <div class="nav-left">
            <a href="#">Dashboard</a>
            <a href="#">Users</a>
            <a href="#">Settings</a>
            <a href="admin_page.php" style="color:#9400e3">Home</a>
        </div>
        <div class="nav-right">
            <a href="php/logout.php" class="btn" style="background-color:#9400e3">Logout</a>
        </div>
    </div>

    <div class="sidebar">
        <a href="admin_page.php">Home</a>
        <a href="#">Profile</a>
        <a href="#">Manage User</a>
        <a href="products_page.php">Products</a>
        <a href="analytics.php">Analytics</a>
        <a href="admin_page_orders.php">View Orders</a>
    </div>

    <div class="main-content">
        <h2>Analytics Dashboard</h2>
        <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
            <div id="userTypesChart" style="width: 400px; height: 300px;"></div>
            <div id="productCategoriesChart" style="width: 400px; height: 300px;"></div>
            <div id="ordersPerUserChart" style="width: 400px; height: 300px;"></div>
        </div>
    </div>

</body>
</html>
