<?php

namespace Controllers;
use Constants\Rules;
use CustomExceptions\ResourceNotFound;
use CustomExceptions\UnAuthorizedException;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use CustomExceptions\BadRequestException;
use Mixin\AuthenticateUser;
use Models\Like;
use Models\Post;
use Models\User;

class PostController extends BaseController
{
    use AuthenticateUser;
    protected $validationSchema = [
        "index" => [
            "url" => [
                "userId" => [Rules::INTEGER]
            ]
        ],
        "create" => [
            "payload" => [
                "content" => [Rules::STRING, Rules::REQUIRED]
            ]
        ],
        "update" => [
            "url" => [
                "postId" => [Rules::INTEGER]
            ],
            "payload" => [
                "content" => [Rules::STRING]
            ]
        ],
        "like" => [
            "url" => [
                "userId" => [Rules::REQUIRED, Rules::INTEGER],
                "postId" => [Rules::INTEGER]
            ]
        ]
    ];

    public function __construct()
    {
        $this->skipHandlers = ["show"];
        parent::__construct();
    }

    /**
     * @throws ResourceNotFound
     */
    //GET users/{userId}/posts
    protected function index($userId)
    {
        $user = ResourceHelper::findResourceOr404Exception(User::class, $userId);

        $paginatedPosts = ResourceHelper::getPaginatedResource(
            $user->posts(),
            ["id", "content", "created", "user_id"],
            ['user:id,name,profile_img', 'likes', 'comments', 'comments.user:id,name,profile_img'],
        );

        $posts = ResourceHelper::loadOnlyForList(
            ["id", "content", "created", "publisher_user", "likes_count", "recent_likes", "comments_count", "recent_comments"],
            $paginatedPosts
        );

        return $posts;

    }

//GET posts/{postId}

    /**
     * @throws ResourceNotFound
     */
    protected function show($postId)
    {
        $post = ResourceHelper::findResourceOr404Exception(
            Post::class,
            $postId
            ,['user:id,name,profile_img', 'likes', 'comments','comments.user:id,name,profile_img']);


        return ResourceHelper::loadOnly(
            ["id", "content", "created", "publisher_user", "likes_count", "recent_likes", "comments_count", "recent_comments"],
            $post);
    }

    /**
     * @throws ResourceNotFound
     */
    // POST posts
    protected function create()
    {


        $payload = RequestHelper::getRequestPayload();

        $post =  $this->authenticatedUser->posts()->create([
            "content" => $payload["content"]
        ]);

        return ["message" => "post has been successfully created  within id = $post->id "];

    }

    /**
     * @throws ResourceNotFound
     * @throws UnAuthorizedException
     */
   // PUT posts/{postId}
    protected function update($postId)
    {
        $post = ResourceHelper::findResourceOr404Exception(Post::class, $postId);

        $this->authenticatedUser->validateIsUserAuthorizedTo($post);

        $payload = RequestHelper::getRequestPayload();
        $post->update($payload);

        return ["message" => "post has been successfully updated."];
    }

    /**
     * @throws ResourceNotFound
     * @throws UnAuthorizedException
     */
     // DELETE posts/{postId}
    protected function delete($postId)
    {

        $post = ResourceHelper::findResourceOr404Exception(Post::class, $postId);
        $this->authenticatedUser->validateIsUserAuthorizedTo($post);

        $post->delete();
        return ["message" => "post  has been deleted successfully"];
    }

    /**
     * @throws BadRequestException
     * @throws ResourceNotFound
     */
     // POST users/{userId}/posts/{postId}/like
    protected function like($userId, $postId)
    {

        $user = ResourceHelper::findResourceOr404Exception(User::class, $userId);
        $post = ResourceHelper::findResourceOr404Exception(Post::class, $postId);

        $isLikeExists = Like::query()->where("user_id", $user->id)->where("post_id", $post->id)->exists();
        if ($isLikeExists) {

            throw new BadRequestException("this user (" . $user->username . ") is already like the post.");
        }

        Like::create([
            "user_id" => $user->id,
            "post_id" => $post->id
        ]);

        return ["message" => "User (" . $user->username . ") like the post that have (" . $post->content . ") as content."];
    }


    /**
     * @throws BadRequestException
     * @throws ResourceNotFound
     */
     // POST users/{userId}/posts/{postId}/unlike
    protected function unLike($userId, $postId)
    {

        $user = ResourceHelper::findResourceOr404Exception(User::class, $userId);
        $post = ResourceHelper::findResourceOr404Exception(Post::class, $postId);

        $like = Like::query()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($like == null) {

            throw new BadRequestException("this user (" . $user->username . ") should be liked the post first to remove his like.");
        }

        $like->delete();
        return ["message" => "User (" . $user->username . ") un-like the post that have (" . $post->content . ") as content."];

    }
}
