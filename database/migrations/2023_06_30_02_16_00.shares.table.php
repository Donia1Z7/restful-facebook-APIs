<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create("shares", function (Blueprint $table) {

    $table->id();

    $table->foreignId("post_id")
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId("user_id")
        ->constrained()
        ->cascadeOnDelete();

    $table->timestamp("created")->useCurrent();
});