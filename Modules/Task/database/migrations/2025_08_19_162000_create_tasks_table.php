<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Task\Enum\TaskType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained()->cascadeOnDelete();
            $table->morphs('taskable');
            $table->enum('type', TaskType::array());
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->integer('order')->default(0);
            $table->boolean('need_review')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
