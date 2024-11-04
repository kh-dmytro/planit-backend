<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed'])->default('active');
        });
    }

    public function down()
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
