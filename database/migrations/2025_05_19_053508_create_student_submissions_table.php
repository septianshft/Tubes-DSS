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
        Schema::create('student_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('scholarship_batch_id')->constrained('scholarship_batches')->cascadeOnDelete();
            $table->foreignId('submitted_by_teacher_id')->constrained('users');
            $table->timestamp('submission_date')->useCurrent();
            $table->json('raw_criteria_values'); // Student's criteria values at submission time
            $table->json('normalized_scores')->nullable(); // Normalized score for each criterion
            $table->decimal('final_saw_score', 8, 4)->nullable(); // Final weighted score
            $table->string('status')->default('pending_review'); // E.g., 'pending_review', 'shortlisted', 'awarded', 'rejected'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_submissions');
    }
};
