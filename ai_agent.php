<?php
$page_title = 'AI Agent';
require_once('includes/load.php');
if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-flash"></span>
                    <span>AI Agent</span>
                </strong>
            </div>
            <div class="panel-body">
                <h1>AI Agent Dashboard</h1>
                <p>This feature is coming soon!</p>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>