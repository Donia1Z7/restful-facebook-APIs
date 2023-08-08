<?php

namespace Controllers;

use CustomExceptions\ResourceNotFound;
use Helpers\ResourceHelper;
use Models\Like;
use Models\Post;

class LikeController extends BaseController
{
    // GET posts/{postId}/likes
    /**
     * @throws ResourceNotFound
     */
    protected function index($postId){

        $limit = key_exists("limit", $_GET) ? $_GET["limit"] : 10;
        $current_page = key_exists("page", $_GET) ? $_GET["page"] : 1;

        $post = ResourceHelper::findResourceOr404Exception(Post::class,$postId);

        return ResourceHelper::getPaginatedResource(

            $post->likes(),
            ["id", "created", "user_id","post_id"],
            ['user:id,name,profile_img'],
        );
    }

}