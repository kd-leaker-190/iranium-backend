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
        Schema::create('action_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('depends_on_action_id')->constrained('actions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_dependencies');
    }
};
