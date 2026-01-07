<?php
require_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    die("Please login first.");
}

$user = current_user();
echo "<h1>Promoting User to Admin...</h1>";
echo "Current User: {$user['name']} ({$user['email']})<br>";
echo "Current Level: {$user['user_level']}<br>";

if ($user['level'] === '1') {
    echo "Already an Admin!<br>";
} else {
    $db->update('users', $user['id'], ['user_level' => '1']);
    echo "<strong>Success! Promoted to Level 1 (Admin).</strong><br>";
    echo "Please <a href='logout.php'>Logout</a> and Login again to see changes.";
}
?>