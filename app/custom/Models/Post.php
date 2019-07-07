<?php
namespace app\custom\Models;

use app\framework\Component\Database\Model\Model;

class Post extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
