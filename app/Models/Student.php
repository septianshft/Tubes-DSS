<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'nisn',
        'date_of_birth',
        'address',
        'email',
        'phone',
        'extracurricular_position',
        'extracurricular_activeness',
        'class_attendance_percentage',
        'average_score',
        'tuition_payment_delays',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the teacher that owns the student.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the submissions for the student.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(StudentSubmission::class);
    }
}
