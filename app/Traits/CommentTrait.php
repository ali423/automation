<?php
namespace App\Traits;

use App\Models\Comment;

trait CommentTrait
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
