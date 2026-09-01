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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->cascadeOnDelete();

            $table->string('subject');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->enum('status', ['waiting_for_admin', 'waiting_for_team', 'closed']);
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
