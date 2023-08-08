<?php

namespace Models;
use CustomExceptions\UnAuthorizedException;

use Controllers\PostController;
class User extends BaseModel
{
    protected $hidden = ["password"];

    public function posts(){
        return $this->hasMany(Post::class);
    }
    public function validateIsUserAuthorizedTo($resource, $customField = "") {

        $customField = $customField ?: "user_id";

        if ($this->id != $resource->$customField) {

            throw new UnAuthorizedException();
        }

    }
}