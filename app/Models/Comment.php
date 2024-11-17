<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;


    protected $fillable = ['card_id', 'user_id', 'content', 'parent_id'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с дочерними комментариями (ответами)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    // Связь с родительским комментарием
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
