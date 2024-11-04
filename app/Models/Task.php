<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'completed', 'checklist_id'];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
