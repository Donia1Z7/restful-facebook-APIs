<?php
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create("comments",function (Blueprint $table){

    $table->id();
    $table->text("content");

    $table->foreignId("post_id")
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId("user_id")
        ->constrained()
        ->cascadeOnDelete();

    $table->timestamp("created")->useCurrent();
});
