<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
Dotenv::createImmutable(__DIR__)->safeLoad();
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection([
    'driver' => $_ENV['DB_DRIVER'],
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASS'],
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();