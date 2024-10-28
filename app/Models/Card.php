<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;
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
