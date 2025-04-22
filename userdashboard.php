<?php
require_once('includes/load.php');
#page_require_level(3); // Require appropriate access level
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory & Sales Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 1.2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .card h2 {
            color: #2c3e50;
            font-size: 1.4rem;
            margin: 0 0 0.5rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .chart-container {
            height: 280px;
            margin-top: 0rem;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .inventory-table th, 
        .inventory-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-adequate { background: #4CAF50; }
        .status-low { background: #FF5722; }

        .metric-card {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sales Metrics -->
        <div class="card">
            <h2><i class="fas fa-chart-line"></i> Sales Overview</h2>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Inventory Metrics -->
        <div class="card">
            <h2><i class="fas fa-boxes"></i> Inventory Status</h2>
            <div class="chart-container">
                <canvas id="inventoryChart"></canvas>
            </div>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Reorder Point</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $inventory = get_inventory_data();
                    foreach ($inventory as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['reorder_point'] ?></td>
                        <td>
                            <span class="status-indicator <?= $item['status_class'] ?>"></span>
                            <?= $item['status'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Key Metrics Cards -->
        <div class="metric-card">
            <i class="fas fa-coins fa-2x"></i>
            <div class="metric-value">$<?= number_format(get_total_sales()) ?></div>
            <p>Total Sales (30 Days)</p>
        </div>

        <div class="metric-card">
            <i class="fas fa-cubes fa-2x"></i>
            <div class="metric-value"><?= get_low_stock_count() ?></div>
            <p>Products Needing Reorder</p>
        </div>
    </div>

    <script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(get_sales_dates()) ?>,
            datasets: [{
                label: 'Daily Sales',
                data: <?= json_encode(get_sales_values()) ?>,
                borderColor: '#667eea',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(102, 126, 234, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Inventory Chart
    const invCtx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(invCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(get_inventory_labels()) ?>,
            datasets: [{
                label: 'Current Stock',
                data: <?= json_encode(get_inventory_values()) ?>,
                backgroundColor: '#4CAF50',
                borderColor: '#45a049',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    </script>

<?php
// Database functions
function get_inventory_data() {
    $db = new MySqli_DB();
    $sql = "SELECT p.name, p.quantity, 
            CEIL(COALESCE(SUM(s.qty), 0) * 1.5) AS reorder_point,
            CASE WHEN p.quantity > CEIL(COALESCE(SUM(s.qty), 0) * 1.5) 
                THEN 'Adequate' ELSE 'Low' END AS status,
            CASE WHEN p.quantity > CEIL(COALESCE(SUM(s.qty), 0) * 1.5) 
                THEN 'status-adequate' ELSE 'status-low' END AS status_class
            FROM products p
            LEFT JOIN sales s ON p.id = s.product_id
                AND s.date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            GROUP BY p.id";
    
    $result = $db->query($sql);
    return $db->while_loop($result); // Use existing while_loop method
}

function get_total_sales() {
    $db = new MySqli_DB();
    $result = $db->query("SELECT SUM(price) AS total FROM sales WHERE date >= NOW() - INTERVAL 30 DAY");
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

function get_low_stock_count() {
    $db = new MySqli_DB();
    $result = $db->query("SELECT COUNT(*) AS count FROM products WHERE quantity <= (
        SELECT CEIL(COALESCE(SUM(s.qty), 0) * 1.5)
        FROM sales s 
        WHERE s.product_id = products.id
        AND s.date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
    )");
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

function get_sales_dates() {
    return array_map(function($d) {
        return date('M j', strtotime("-$d days"));
    }, range(29, 0));
}

function get_sales_values() {
    $db = new MySqli_DB();
    // Replace this with your actual DB query for real data
    return array_fill(0, 30, rand(500, 2000));
}

function get_inventory_labels() {
    $db = new MySqli_DB();
    $result = $db->query("SELECT name FROM products");
    $labels = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['name'];
    }
    return $labels;
}

function get_inventory_values() {
    $db = new MySqli_DB();
    $result = $db->query("SELECT quantity FROM products");
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row['quantity'];
    }
    return $values;
}
?>
</body>
</html>
