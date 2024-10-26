<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
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
