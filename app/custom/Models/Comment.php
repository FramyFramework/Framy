<?php
namespace app\custom\Models;

use app\framework\Component\Database\Model\Model;

class Comment extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, "id");
    }

    public function post()
    {
        return $this->belongsTo(Post::class, "post_id");
    }
}
