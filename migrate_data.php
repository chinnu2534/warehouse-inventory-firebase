<?php
require_once('includes/load.php');

echo "<h1>Starting Data Migration...</h1>";

// 1. Migrate User Groups
$groups = [
    ['group_name' => 'Admin', 'group_level' => '1', 'group_status' => '1'],
    ['group_name' => 'Special', 'group_level' => '2', 'group_status' => '1'],
    ['group_name' => 'User', 'group_level' => '3', 'group_status' => '1']
];

echo "<h2>Migrating User Groups...</h2>";
foreach ($groups as $group) {
    // Check if exists
    $existing = $db->find_by_field('user_groups', 'group_level', $group['group_level']);
    if (!$existing) {
        $id = $db->insert('user_groups', $group);
        echo "Created group: {$group['group_name']} (ID: $id)<br>";
    } else {
        echo "Group {$group['group_name']} already exists.<br>";
    }
}

// 2. Migrate Initial Users
// Password hashes from SQL dump are SHA1. 
// Note: Firebase Auth handles passwords separately, but for legacy support/testing we verify against 'password' field in Firestore
$users = [
    [
        'name' => 'Admin User',
        'username' => 'admin',
        'password' => 'd033e22ae348aeb5660fc2140aec35850c4da997', // admin
        'user_level' => '1',
        'image' => 'no_image.jpg',
        'status' => '1',
        'email' => 'admin@localhost.com' // Dummy email for Firebase mapping
    ],
    [
        'name' => 'Special User',
        'username' => 'special',
        'password' => 'ba36b97a41e7faf742ab09bf88405ac04f99599a', // special
        'user_level' => '2',
        'image' => 'no_image.jpg',
        'status' => '1',
        'email' => 'special@localhost.com'
    ],
    [
        'name' => 'Default User',
        'username' => 'user',
        'password' => '12dea96fec20593566ab75692c9949596833adc9', // user
        'user_level' => '3',
        'image' => 'no_image.jpg',
        'status' => '1',
        'email' => 'user@localhost.com'
    ]
];

echo "<h2>Migrating Users...</h2>";
foreach ($users as $user) {
    $existing = $db->find_by_field('users', 'username', $user['username']);
    if (!$existing) {
        $id = $db->insert('users', $user);
        echo "Created user: {$user['username']} (ID: $id)<br>";
    } else {
        echo "User {$user['username']} already exists.<br>";
    }
}

echo "<h1>Migration Complete!</h1>";
echo "<p><a href='index.php'>Go to Login</a></p>";
?>