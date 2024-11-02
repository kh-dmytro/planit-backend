<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Checklist extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'card_id'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
