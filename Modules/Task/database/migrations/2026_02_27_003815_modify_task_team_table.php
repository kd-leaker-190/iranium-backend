<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('task_team', function (Blueprint $table) {
            $table->string('status')->default('done'); // done | skipped
            $table->timestamp('done_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('task_team', function (Blueprint $table) {
            $table->dropColumn(['status', 'done_at', 'skipped_at']);
        });
    }
};
