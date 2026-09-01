<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('name');
            $table->string('last_name');
            $table->string('grade_level');
            $table->date('birth_date');
            $table->string('national_code')->nullable();
            $table->enum('role', [
                'successor', // جانشین
                'human_resource', // نیروی انسانی
                'academic', // علمی تحصیلی
                'constructiveness_and_efficiency', // سازندگی و کارآمدی
                'cultural', // فرهنگی
                'education_and_training_of_famous_assistants', // تعلیم و تربیت یاوران معروف
                'journalism', // خبرنگاری
                'support', // پشتیبانی
                'inspection', // بازرسی
                'physical_education' // تربیت بدنی
            ])->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_users');
    }
};
