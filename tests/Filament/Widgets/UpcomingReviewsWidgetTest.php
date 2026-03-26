<?php

use App\Enums\ReviewStatus;
use App\Filament\Widgets\UpcomingReviewsWidget;
use App\Models\HR\Employee;
use App\Models\HR\PerformanceReview;
use Livewire\Livewire;

it('renders the upcoming reviews widget', function () {
    Livewire::test(UpcomingReviewsWidget::class)
        ->assertOk();
});

it('shows reviews due within the next 30 days', function () {
    $employee = Employee::factory()->create();

    $upcoming = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Draft,
        'due_date' => now()->addDays(10),
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    $far = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Draft,
        'due_date' => now()->addDays(60),
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(UpcomingReviewsWidget::class)
        ->assertCanSeeTableRecords([$upcoming])
        ->assertCanNotSeeTableRecords([$far]);
});

it('shows overdue reviews', function () {
    $employee = Employee::factory()->create();

    $overdue = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Submitted,
        'due_date' => now()->subDays(5),
        'review_period_start' => now()->subMonths(6),
        'review_period_end' => now()->subMonths(3),
    ]);

    Livewire::test(UpcomingReviewsWidget::class)
        ->assertCanSeeTableRecords([$overdue]);
});

it('does not show acknowledged reviews', function () {
    $employee = Employee::factory()->create();

    $acknowledged = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Acknowledged,
        'due_date' => now()->addDays(5),
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(UpcomingReviewsWidget::class)
        ->assertCanNotSeeTableRecords([$acknowledged]);
});

it('shows empty state when no upcoming reviews', function () {
    Livewire::test(UpcomingReviewsWidget::class)
        ->assertSee('No upcoming reviews');
});
