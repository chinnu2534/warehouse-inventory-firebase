<?php
require_once('includes/load.php');
include_once('layouts/header.php');
require_once('includes/functions.php'); // Include functions.php

// Fetch Data
$sales_data = find_sales_by_month();
$products_low = find_low_stock_products();
$top_products = find_top_selling_products(5);
$recent_sales = find_recent_sales(10);
$stock_status = calculate_stock_status();
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-stats"></span>
                    <span>Inventory Analytics Dashboard</span>
                </strong>
            </div>
            <div class="panel-body">
                
                <!-- Sales by Month Chart -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5>Monthly Sales Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Stock Status Chart -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5>Stock Status</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="stockChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Reorder Point Analysis -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5>Reorder Point Analysis</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Reorder Point</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products_low as $product): ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo $product['quantity']; ?></td>
                                        <td>100</td>
                                        <td>
                                            <span class="label label-<?php echo ($product['quantity'] < 50) ? 'danger' : 'warning'; ?>">
                                                <?php echo ($product['quantity'] < 50) ? 'Critical' : 'Low'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Products -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5>Top Selling Products</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="topProductsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Sales by Month Chart
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($sales_data, 'month')); ?>,
        datasets: [{
            label: 'Sales Value ($)',
            data: <?php echo json_encode(array_column($sales_data, 'total_sales')); ?>,
            borderColor: '#4e73df',
            tension: 0.1
        }]
    }
});

// Stock Status Chart
new Chart(document.getElementById('stockChart'), {
    type: 'doughnut',
    data: {
        labels: ['Low Stock', 'Adequate Stock'],
        datasets: [{
            data: <?php echo json_encode([$stock_status['low_stock'], $stock_status['adequate_stock']]); ?>,
            backgroundColor: ['#dc3545', '#28a745']
        }]
    }
});

// Top Products Chart
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($top_products, 'name')); ?>,
        datasets: [{
            label: 'Units Sold',
            data: <?php echo json_encode(array_column($top_products, 'total_sold')); ?>,
            backgroundColor: '#1cc88a'
        }]
    }
});
</script>

<style>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border-radius: 0.35rem;
}
.card-header {
    border-radius: 0.35rem 0.35rem 0 0;
}
</style>

<?php include_once('layouts/footer.php'); ?>