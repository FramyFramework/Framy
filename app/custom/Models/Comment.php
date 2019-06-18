<?php
namespace app\custom\Models;

use app\framework\Component\Database\Model\Model;

class Comment extends Model
{
    public function byUser()
    {
        return $this->hasOne(User::class);
    }
}
