<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipBatch extends Model
{
    /** @use HasFactory<\Database\Factories\ScholarshipBatchFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'criteria_config',
    ];

    protected $casts = [
        'criteria_config' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the submissions for the scholarship batch.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(StudentSubmission::class);
    }
}
