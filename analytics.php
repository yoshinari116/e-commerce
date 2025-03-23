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

$stmt = $conn->prepare("SELECT products_tbl.pt_brand, COUNT(*) AS count FROM order_tbl 
                        JOIN products_tbl ON order_tbl.product_id = products_tbl.product_id 
                        GROUP BY products_tbl.pt_brand");
$stmt->execute();
$orders_by_brand = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/analytics.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawCharts);

      function drawCharts() {
        drawUserTypesChart();
        drawProductCategoriesChart();
        drawOrdersPerUserChart();
        drawOrdersByBrandChart();
      }

      function drawUserTypesChart() {
        var data = google.visualization.arrayToDataTable([
          ['User Role', 'Count'],
          <?php
            foreach ($user_types as $row) {
                echo "['".$row['role']."', ".$row['count']."],";
            }
          ?>
        ]);

        var options = { title: 'User Role Distribution', 
                        is3D: true,  
                        backgroundColor: { fill: 'none' },
                        colors: ['#9400E3', '#6A00BF', '#450099', '#003D99', '#005FAD', '#2E2E5E', '#50505A', '#A0A0A0']   
                      };
        var chart = new google.visualization.PieChart(document.getElementById('userTypesChart'));
        chart.draw(data, options);
      }

      function drawProductCategoriesChart() {
        var data = google.visualization.arrayToDataTable([
          ['Category', 'Count'],
          <?php
            foreach ($product_categories as $row) {
                echo "['".$row['pt_type']."', ".$row['count']."],";
            }
          ?>
        ]);

        var options = { title: 'Product Categories', 
                        is3D: true,  
                        backgroundColor: { fill: 'none' },
                        colors: ['#9400E3', '#6A00BF', '#450099', '#003D99', '#005FAD', '#2E2E5E', '#50505A', '#A0A0A0']
                      };
        var chart = new google.visualization.PieChart(document.getElementById('productCategoriesChart'));
        chart.draw(data, options);
      }

      function drawOrdersPerUserChart() {
        var data = google.visualization.arrayToDataTable([
          ['User', 'Orders'],
          <?php
            foreach ($orders_per_user as $row) {
                echo "['".$row['username']."', ".$row['count']."],";
            }
          ?>
        ]);

        var options = { title: 'Orders Per User', 
                        is3D: true, 
                        backgroundColor: { fill: 'none' },
                        colors: ['#9400E3', '#6A00BF', '#450099', '#003D99', '#005FAD', '#2E2E5E', '#50505A', '#A0A0A0']
                      };
        var chart = new google.visualization.PieChart(document.getElementById('ordersPerUserChart'));
        chart.draw(data, options);
      }

      function drawOrdersByBrandChart() {
        var data = google.visualization.arrayToDataTable([
          ['Brand Name', 'Orders'],
          <?php
            foreach ($orders_by_brand as $row) {
                echo "['".$row['pt_brand']."', ".$row['count']."],";
            }
          ?>
        ]);

        var options = { title: 'Orders by Brand', 
                        is3D: true, 
                        backgroundColor: { fill: 'none' },
                        colors: ['#9400E3', '#6A00BF', '#450099', '#003D99', '#005FAD', '#2E2E5E', '#50505A', '#A0A0A0']
                      };
        var chart = new google.visualization.PieChart(document.getElementById('ordersByBrandChart'));
        chart.draw(data, options);
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

    <div class="container">
    <h1>Analytics Charts</h1>
    <div class="row">
        <div class="col-md-6 offset-md-1"> <!-- Added offset-md-1 -->
            <div id="userTypesChart" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="col-md-5">
            <div id="productCategoriesChart" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 offset-md-1">
            <div id="ordersPerUserChart" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="col-md-5">
            <div id="ordersByBrandChart" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
</div>

</div>

<style>
    .charts-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 20px;
    }
    .chart-box {
        width: 400px;
        height: 300px;
        /* background: transparent; */
        padding: 10px;
        border-radius: 8px;
        /* box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); */
    }
    #userTypesChart, #productCategoriesChart, #ordersByBrandChart, #ordersPerUserChart {
        width: 100%;
        height: 100%;
    }
</style>


</body>
</html>
