<?php
  $page_title = 'Daily Sales';
  require_once('includes/load.php');
  // Check if the user has permission to view this page
  page_require_level(3);
?>

<?php
  // Get current year and month
  $year  = date('Y');
  $month = date('m');
  // Fetch daily sales data for today's date
  $sales = dailySales(); // You no longer need $year and $month as parameters
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Daily Sales</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th> Product Name </th>
              <th class="text-center" style="width: 15%;"> Quantity Sold</th>
              <th class="text-center" style="width: 15%;"> Price </th>
              <th class="text-center" style="width: 15%;"> Total </th>
              <th class="text-center" style="width: 15%;"> Date </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
              <td class="text-center"><?php echo count_id(); ?></td>
              <td><?php echo remove_junk($sale['name']); ?></td>
              <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
              <td class="text-center"><?php echo number_format($sale['price'], 2); ?></td> <!-- Display price -->
              <td class="text-center"><?php echo number_format($sale['total_saleing_price'], 2); ?></td>
              <td class="text-center"><?php echo date('Y-m-d'); ?></td> <!-- Display today's date -->
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
