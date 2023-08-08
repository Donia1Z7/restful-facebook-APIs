<?php

namespace Models;
use Controllers\CommentController;

class Post extends BaseModel
{
    protected $hidden = ['user_id'];
    protected $appends = ['publisher_user','likes_count','comments_count','recent_likes','recent_comments'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getPublisherUserAttribute(){
        return $this->user;
    }

    public function getLikesCountAttribute(){
        return sizeof($this->likes);
    }

    public function getCommentsCountAttribute(){
        return sizeof($this->comments);
    }

    public function getRecentLikesAttribute()
    {

        $recent_likes = [];

        foreach ($this->likes->sortByDesc('created') as $like) {

            $recent_likes[] = $like->user->name;

            if (sizeof($recent_likes) == 2) {
                break;
            }
        }
        return $recent_likes;
    }

    public function getRecentCommentsAttribute()
    {

        $recent_comments = [];

        foreach ($this->comments->sortByDesc('created') as $comment) {

            $recent_comments[] = [
                'id' => $comment->id,
                'content' => $comment->content,
                'created' => $comment->created,
                'user' => $comment->user
            ];

            if (sizeof($recent_comments) == 5) {
                break;

            }
        }
        return $recent_comments;
    }
}
