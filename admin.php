<?php
$page_title = 'Admin Dashboard';
require_once('includes/load.php');
page_require_level(1);

// Fetch necessary data
$c_categorie     = count_by_id('categories');
$c_product       = count_by_id('products');
$c_sale          = count_by_id('sales');
$c_user          = count_by_id('users');
$products_sold   = find_higest_saleing_product('10');
$recent_products = find_recent_product_added('5');
$recent_sales    = find_recent_sale_added('5');
?>

<?php include_once('layouts/header.php'); ?>

<div class="dashboard">
    <!-- Display Messages -->
    <div class="row">
        <div class="col-md-12">
            <?php echo display_msg($msg); ?>
        </div>
    </div>

    <!-- Dashboard Metrics -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="dashboard-card users-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h2><?php echo $c_user['total']; ?></h2>
                        <p>Registered Users</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="dashboard-card categories-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="card-content">
                        <h2><?php echo $c_categorie['total']; ?></h2>
                        <p>Product Categories</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="dashboard-card products-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="card-content">
                        <h2><?php echo $c_product['total']; ?></h2>
                        <p>Total Products</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="dashboard-card sales-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h2><?php echo $c_sale['total']; ?></h2>
                        <p>Completed Sales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="welcome-banner">
                <h1>Welcome to SSDI Inventory Manager 2025</h1>
                <p>Smart Inventory Management System</p>
            </div>
        </div>
    </div>

    <!-- Data Sections -->
    <div class="row">
        <!-- Top Selling Products -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> Top Selling Products</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sold Units</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_sold as $product_sold): ?>
                            <tr>
                                <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                                <td><?php echo (int)$product_sold['totalSold']; ?></td>
                                <td>$<?php echo (int)$product_sold['totalQty']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> Recent Transactions</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_sales as $recent_sale): ?>
                            <tr>
                                <td><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></td>
                                <td><?php echo remove_junk(first_character($recent_sale['name'])); ?></td>
                                <td>$<?php echo remove_junk(first_character($recent_sale['price'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<style>
.dashboard {
    padding: 2rem;
    background: #f8fafc;
    min-height: 100vh;
}

.dashboard-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    border: none;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

.card-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(0,0,0,0.05);
}

.card-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.5rem;
    color: white;
}

.users-card .card-icon { background: #6366f1; }
.categories-card .card-icon { background: #10b981; }
.products-card .card-icon { background: #f59e0b; }
.sales-card .card-icon { background: #ef4444; }

.welcome-banner {
    background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
    border-radius: 15px;
    padding: 3rem 2rem;
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.welcome-banner h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.welcome-banner p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 0.5rem;
}

.data-table thead th {
    background: #f1f5f9;
    color: #64748b;
    padding: 1rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.data-table tbody td {
    background: white;
    padding: 1rem;
    border: none;
    box-shadow: 0 2px 3px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
}

.data-table tbody tr:hover td {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .dashboard {
        padding: 1rem;
    }
    
    .welcome-banner h1 {
        font-size: 2rem;
    }
    
    .data-table {
        display: block;
        overflow-x: auto;
    }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.dashboard-card {
    animation: fadeIn 0.6s ease forwards;
}
</style>

<script>
// Add smooth scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});
</script>