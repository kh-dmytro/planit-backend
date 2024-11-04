<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Board extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'board_user')->withPivot('role')->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cards() 
    {
        return $this->hasMany(Card::class);
    }
}
