<?php
  $page_title = 'All Groups';
  require_once('includes/load.php');
  page_require_level(1);
  $all_groups = find_all('user_groups');
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="glyphicon glyphicon-th"></i> User Groups</h3>
        <a href="add_group.php" class="btn btn-success btn-sm">
          <i class="glyphicon glyphicon-plus"></i> Add New Group
        </a>
      </div>
      <div class="card-body p-4">
        <table class="table table-hover table-striped table-bordered text-center">
          <thead class="bg-dark text-white">
            <tr>
              <th style="width: 50px;">#</th>
              <th>Group Name</th>
              <th style="width: 20%;">Group Level</th>
              <th style="width: 15%;">Status</th>
              <th style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($all_groups as $a_group): ?>
            <tr>
              <td><?php echo count_id(); ?></td>
              <td class="font-weight-bold"><?php echo remove_junk(ucwords($a_group['group_name'])); ?></td>
              <td>
                <span class="badge badge-pill 
                  <?php echo ($a_group['group_level'] == 1) ? 'badge-danger' : (($a_group['group_level'] == 2) ? 'badge-warning' : 'badge-info'); ?>">
                  <?php echo remove_junk(ucwords($a_group['group_level'])); ?>
                </span>
              </td>
              <td>
                <?php if ($a_group['group_status'] === '1'): ?>
                  <span class="badge badge-success px-3 py-2">Active</span>
                <?php else: ?>
                  <span class="badge badge-danger px-3 py-2">Inactive</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="btn-group">
                  <a href="edit_group.php?id=<?php echo (int)$a_group['id'];?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Edit">
                    <i class="glyphicon glyphicon-pencil"></i>
                  </a>
                  <a href="delete_group.php?id=<?php echo (int)$a_group['id'];?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove">
                    <i class="glyphicon glyphicon-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<style>
  body {
    background-color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
  }
  .card {
    border-radius: 10px;
    overflow: hidden;
  }
  .card-header {
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
  }
  .bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #6610f2);
  }
  .table-hover tbody tr:hover {
    background-color: #f1f1f1;
  }
  .badge {
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
  }
  .btn-group .btn {
    margin-right: 5px;
    border-radius: 5px;
  }
  .btn-sm {
    padding: 8px 10px;
  }
</style>
