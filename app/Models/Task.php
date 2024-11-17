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
    /*
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($task) {
            // Обновляем статус чеклиста при изменении статуса задачи
            $task->checklist->updateStatusBasedOnTasks(); // Убедитесь, что этот метод существует
            $task->checklist->card->updateStatusBasedOnChecklists(); // Также обновляем статус карты
        });
    }
        */
}
