<?php

namespace Controllers;
use Constants\Rules;
use CustomExceptions\ResourceNotFound;
use CustomExceptions\UnAuthorizedException;
use Helpers\RequestHelper;
use Helpers\ResourceHelper;
use Mixin\AuthenticateUser;
use Models\Comment;
use Models\Post;
use Models\User;

class CommentController extends BaseController
{
    use AuthenticateUser;

    protected $validationSchema = [
        "create" => [
            "url" => [
                "userId" => [Rules::INTEGER],
                "postId" => [Rules::INTEGER]
            ],
            "payload" => [
                "content" => [Rules::REQUIRED, Rules::STRING]
            ]
        ],
        "update" => [
            "payload" => [
                "content" => [Rules::REQUIRED, Rules::STRING]
            ]
        ]
    ];



    /**
     * @throws ResourceNotFound
     */

    //GET posts/{postId}/comments
    protected function index($postId){

        $post = ResourceHelper::findResourceOr404Exception(Post::class,$postId);

        return ResourceHelper::getPaginatedResource(
            $post
            ->comments(),
            ["id", "content", "created", "user_id","post_id"],
            ['user:id,name,profile_img']
        );

    }

    //POST users/{userId}/posts/{postId}/comments

    /**
     * @throws ResourceNotFound
     */
    protected function create($userId, $postId) {

        $user =  ResourceHelper::findResourceOr404Exception(User::class, $userId);
        $post = ResourceHelper::findResourceOr404Exception(Post::class, $postId);

        $payload = RequestHelper::getRequestPayload();
        $payload["user_id"] = $user->id;
        $payload["post_id"] = $post->id;

        Comment::create($payload);

        return ["message" => "comment has been successfully created."];
    }

    /**
     * @throws ResourceNotFound
     * @throws UnAuthorizedException
     */
    //PUT comments/{commentId}
    protected function update($commentId) {

        $comment = ResourceHelper::findResourceOr404Exception(Comment::class, $commentId);
        $payload = RequestHelper::getRequestPayload();

        $this->authenticatedUser->validateIsUserAuthorizedTo($comment,"user_id");

        $content = $payload['content'];
        $comment->update([
            "content" => $content
        ]);
        return ["message" => "comment has been successfully updated."];
    }

    /**
     * @throws UnAuthorizedException
     * @throws ResourceNotFound
     */
    protected function delete($commentId) {

        $comment = ResourceHelper::findResourceOr404Exception(Comment::class, $commentId);

        $this->authenticatedUser->validateIsUserAuthorizedTo($comment,"user_id");

        $comment->delete();

        return ["message" => "comment has been successfully deleted."];
    }
}