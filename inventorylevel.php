<?php 
include_once('auth.php');
include_once('db_connection.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    
    
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="glyphicon glyphicon-list-alt"></i>
                                Current Inventory Levels
                            </h3>
                        </div>
                        
                        <div class="panel-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Stock Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT id, name, quantity FROM products ORDER BY name ASC";
                                    $result = mysqli_query($conn, $query);
                                    
                                    if(mysqli_num_rows($result) > 0) {
                                        $count = 1;
                                        while($row = mysqli_fetch_assoc($result)) {
                                            $status = '';
                                            $quantity = $row['quantity'];
                                            
                                            if($quantity == 0) {
                                                $status = '<span class="label label-danger">Out of Stock</span>';
                                            } elseif($quantity < 10) {
                                                $status = '<span class="label label-warning">Low Stock</span>';
                                            } else {
                                                $status = '<span class="label label-success">In Stock</span>';
                                            }
                                            
                                            echo '<tr>
                                                    <td>'.$count.'</td>
                                                    <td>'.$row['name'].'</td>
                                                    <td>'.$quantity.'</td>
                                                    <td>'.$status.'</td>
                                                  </tr>';
                                            $count++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="4">No products found in inventory</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>