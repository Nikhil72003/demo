<?php

namespace Database\Factories\HR;

use App\Enums\ReviewRating;
use App\Enums\ReviewStatus;
use App\Models\HR\Employee;
use App\Models\HR\PerformanceReview;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<PerformanceReview>
 */
class PerformanceReviewFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = PerformanceReview::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->subMonths(fake()->numberBetween(1, 12))->startOfMonth();
        $end = $start->copy()->addMonths(fake()->numberBetween(3, 6))->endOfMonth();
        $status = fake()->randomElement(ReviewStatus::cases());

        return [
            'employee_id' => Employee::factory(),
            'reviewer_id' => Employee::factory(),
            'review_period' => $start->format('Q') === '1' ? 'Q1 ' . $start->year :
                ($start->format('Q') === '2' ? 'Q2 ' . $start->year :
                    ($start->format('Q') === '3' ? 'Q3 ' . $start->year : 'Q4 ' . $start->year)),
            'review_period_start' => $start,
            'review_period_end' => $end,
            'due_date' => $end->copy()->addDays(fake()->numberBetween(7, 30)),
            'status' => $status,
            'rating' => $status === ReviewStatus::Draft ? null : fake()->randomElement(ReviewRating::cases()),
            'goals_achieved' => fake()->paragraph(),
            'manager_comments' => $status !== ReviewStatus::Draft ? fake()->paragraph() : null,
            'employee_comments' => $status === ReviewStatus::Acknowledged ? fake()->paragraph() : null,
            'reviewed_at' => $status !== ReviewStatus::Draft && $end->lte(now()) ? fake()->dateTimeBetween($end, 'now') : null,
        ];
    }
}
