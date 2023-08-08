<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create("friends", function (Blueprint $table) {

    $table->id();

    $table->foreignId("user_id")
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId("friend_id")
        ->constrained("users")
        ->cascadeOnDelete();

    $table->timestamp("created")->useCurrent();
});