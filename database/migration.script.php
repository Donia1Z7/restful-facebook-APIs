<?php
require 'bootstrap.php';
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

$migrations = glob(__DIR__ . "/migrations/*");


foreach ($migrations as $migration){

    $_ = explode("/",$migration);

    $script_name=array_pop($_);

    $table_name = explode(".",$script_name)[1];

    if (Manager::schema()->hasTable($table_name)){
        echo "$table_name table already exists !\n";
        continue;
    }

    require $migration;
    echo " table has successfully created .\n";

}

