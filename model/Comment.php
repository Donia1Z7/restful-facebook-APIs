<?php

namespace Models;

class Comment extends BaseModel
{
    protected $hidden = ['user_id','post_id'];
public function user(){
     return $this->belongsTo(User::class);
}
}