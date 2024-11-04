<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Checklist extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description','status', 'card_id'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // Метод для обновления статуса чеклиста на основе задач
    public function updateStatusBasedOnTasks()
    {
        $allTasksCompleted = $this->tasks->every(function ($task) {
            return $task->is_completed; // Проверяем поле is_completed
        });

        $this->status = $allTasksCompleted ? 'completed' : 'active';
        $this->save();

        // После обновления статуса чеклиста обновляем статус карты
        $this->card->updateStatusBasedOnChecklists();
    }
}
