<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\StudentSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'scholarship_batch_id',
        'submitted_by_teacher_id',
        'submission_date',
        'raw_criteria_values',
        'normalized_scores',
        'final_saw_score',
        'status',
        'revision_notes',
        'status_updated_at',
        'status_updated_by',
    ];

    protected $casts = [
        'raw_criteria_values' => 'array',
        'normalized_scores' => 'array',
        'submission_date' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the scholarship batch that owns the submission.
     */
    public function scholarshipBatch(): BelongsTo
    {
        return $this->belongsTo(ScholarshipBatch::class);
    }

    /**
     * Get the teacher who submitted the student.
     */
    public function submittedByTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by_teacher_id');
    }

    /**
     * Get the user who last updated the status.
     */
    public function statusUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }
}
