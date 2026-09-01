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
        Schema::create('puzzle_pieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puzzle_id')->constrained('puzzles')->cascadeOnDelete();
            $table->string('piece_data');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puzzle_pieces');
    }
};
