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
        Schema::table('file_upload_team', function (Blueprint $table) {
            $table->boolean('edit_used')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('edited_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('file_upload_team', function (Blueprint $table) {
            $table->dropColumn(['edit_used', 'submitted_at', 'edited_at']);
        });
    }
};
