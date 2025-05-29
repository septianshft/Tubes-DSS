<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon; // Import Carbon

class ScholarshipBatch extends Model
{
    /** @use HasFactory<\Database\Factories\ScholarshipBatchFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status', // This might store the manually set status or a default
        'criteria_config',
        'quota',
    ];

    protected $casts = [
        'criteria_config' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Add an accessor for a computed status
    public function getComputedStatusAttribute(): string
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        if ($now->lt($startDate)) {
            return 'Upcoming';
        } elseif ($now->gte($startDate) && $now->lte($endDate)) {
            return 'Active';
        } else {
            return 'Closed';
        }
    }

    /**
     * Get the submissions for the scholarship batch.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(StudentSubmission::class);
    }
}
