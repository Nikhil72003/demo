<?php

namespace App\Models\HR;

use App\Enums\ReviewRating;
use App\Enums\ReviewStatus;
use Database\Factories\HR\PerformanceReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    /** @use HasFactory<PerformanceReviewFactory> */
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'performance_reviews';

    public function casts(): array
    {
        return [
            'rating' => ReviewRating::class,
            'status' => ReviewStatus::class,
            'review_period_start' => 'date',
            'review_period_end' => 'date',
            'due_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Employee, $this> */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** @return BelongsTo<Employee, $this> */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }
}
