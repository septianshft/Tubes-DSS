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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users');
            $table->string('name');
            $table->string('nisn')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('extracurricular_position')->nullable(); // E.g., 'Chairman', 'Secretary', 'Member', 'None'
            $table->integer('extracurricular_activeness')->nullable(); // E.g., 1-5 scale
            $table->unsignedTinyInteger('class_attendance_percentage')->nullable(); // 0-100
            $table->decimal('average_score', 5, 2)->nullable(); // 0-100.00
            $table->integer('tuition_payment_delays')->nullable(); // Number of late payments or days
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
