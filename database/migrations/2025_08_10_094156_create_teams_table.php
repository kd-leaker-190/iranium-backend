<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_identifier');
            $table->string('name');
            $table->string('content')->nullable();
            $table->string('phone')->nullable();
            $table->string('bio')->nullable();
            $table->integer('score')->default(0);
            $table->integer('coin')->default(0);
            $table->string('hash')->unique();
            $table->boolean('gender')->default(true); // true male
            $table->timestamp('start')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
