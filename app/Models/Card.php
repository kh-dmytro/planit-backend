<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description','status','priority' , 'board_id'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    // App\Models\Card.php

    public function users()
    {
        return $this->belongsToMany(User::class, 'card_user')->withPivot('role')->withTimestamps();
    }


    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Метод для обновления статуса карты на основе чеклистов
    public function updateStatusBasedOnChecklists()
    {
        $allChecklistsCompleted = $this->checklists->every(function ($checklist) {
            return $checklist->status === 'completed';
        });

        $this->status = $allChecklistsCompleted ? 'completed' : 'active';
        $this->save();
    }
}
