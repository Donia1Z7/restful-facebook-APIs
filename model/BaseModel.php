<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = ["id"];
    public $timestamps = false;
}