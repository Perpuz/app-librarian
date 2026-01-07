<?php

// Load CodeIgniter
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require 'system/bootstrap.php';

use App\Models\UserModel;

$model = new UserModel();
$users = $model->findAll();

echo "Users count: " . count($users) . "\n";
foreach ($users as $user) {
    echo "User: " . $user['username'] . "\n";
}
