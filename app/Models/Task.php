<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'completed', 'checklist_id'];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
