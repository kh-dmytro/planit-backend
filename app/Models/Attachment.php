<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['file_path', 'file_name', 'file_type'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
    /* public function user()
    {
        return $this->belongsTo(User::class);
    }
    */
}
