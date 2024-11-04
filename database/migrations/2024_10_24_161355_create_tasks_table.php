<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('due_date')->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->timestamps();

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
