<?php

namespace Models;

class Like extends BaseModel
{
    protected $hidden = ['user_id','post_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}