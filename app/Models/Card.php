<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['title', 'description', 'board_id'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
}
