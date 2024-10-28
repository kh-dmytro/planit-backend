<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('boards', 'name')) {
            Schema::table('boards', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }

        if (Schema::hasColumn('cards', 'name')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }

        if (Schema::hasColumn('checklists', 'name')) {
            Schema::table('checklists', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }

        if (Schema::hasColumn('tasks', 'name')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('boards', 'title')) {
            Schema::table('boards', function (Blueprint $table) {
                $table->renameColumn('title', 'name');
            });
        }

        if (Schema::hasColumn('cards', 'title')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->renameColumn('title', 'name');
            });
        }

        if (Schema::hasColumn('checklists', 'title')) {
            Schema::table('checklists', function (Blueprint $table) {
                $table->renameColumn('title', 'name');
            });
        }

        if (Schema::hasColumn('tasks', 'title')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->renameColumn('title', 'name');
            });
        }
    
    }
};
