<?php

use App\Enums\ReviewRating;
use App\Enums\ReviewStatus;
use App\Filament\Resources\HR\PerformanceReviews\Pages\CreatePerformanceReview;
use App\Filament\Resources\HR\PerformanceReviews\Pages\EditPerformanceReview;
use App\Filament\Resources\HR\PerformanceReviews\Pages\ListPerformanceReviews;
use App\Models\HR\Employee;
use App\Models\HR\PerformanceReview;
use Livewire\Livewire;

it('can list performance reviews', function () {
    $employee = Employee::factory()->create();
    $reviews = PerformanceReview::factory(3)->create([
        'employee_id' => $employee->id,
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(ListPerformanceReviews::class)
        ->assertOk()
        ->assertCanSeeTableRecords($reviews);
});

it('can create a performance review', function () {
    $employee = Employee::factory()->create();
    $reviewer = Employee::factory()->create();

    Livewire::test(CreatePerformanceReview::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'reviewer_id' => $reviewer->id,
            'review_period' => 'Q1 2026',
            'review_period_start' => '2026-01-01',
            'review_period_end' => '2026-03-31',
            'status' => ReviewStatus::Draft,
            'rating' => ReviewRating::MeetsExpectations,
            'goals_achieved' => 'Completed all quarterly goals.',
            'manager_comments' => 'Great performance this quarter.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(PerformanceReview::class, [
        'employee_id' => $employee->id,
        'review_period' => 'Q1 2026',
        'status' => ReviewStatus::Draft,
    ]);
});

it('validates required fields on create', function () {
    Livewire::test(CreatePerformanceReview::class)
        ->fillForm([
            'employee_id' => null,
            'review_period' => null,
            'review_period_start' => null,
            'review_period_end' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'employee_id' => 'required',
            'review_period' => 'required',
            'review_period_start' => 'required',
            'review_period_end' => 'required',
        ]);
});

it('can edit a performance review', function () {
    $employee = Employee::factory()->create();
    $review = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Draft,
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(EditPerformanceReview::class, ['record' => $review->id])
        ->fillForm([
            'status' => ReviewStatus::Submitted,
            'rating' => ReviewRating::Outstanding,
            'manager_comments' => 'Exceptional results this period.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(PerformanceReview::class, [
        'id' => $review->id,
        'status' => ReviewStatus::Submitted,
        'rating' => ReviewRating::Outstanding,
    ]);
});

it('cannot submit a review without rating and manager comments', function () {
    $employee = Employee::factory()->create();
    $review = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Draft,
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(EditPerformanceReview::class, ['record' => $review->id])
        ->fillForm([
            'status' => ReviewStatus::Submitted,
            'rating' => null,
            'manager_comments' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'rating' => 'required',
            'manager_comments' => 'required',
        ]);
});

it('cannot acknowledge a review without employee comments', function () {
    $employee = Employee::factory()->create();
    $review = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Submitted,
        'rating' => ReviewRating::MeetsExpectations,
        'manager_comments' => 'Good work.',
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    Livewire::test(EditPerformanceReview::class, ['record' => $review->id])
        ->fillForm([
            'status' => ReviewStatus::Acknowledged,
            'employee_comments' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'employee_comments' => 'required',
        ]);
});

it('can filter reviews by status', function () {
    $employee = Employee::factory()->create();

    $draft = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Draft,
        'review_period_start' => now()->subMonths(3),
        'review_period_end' => now(),
    ]);

    $acknowledged = PerformanceReview::factory()->create([
        'employee_id' => $employee->id,
        'status' => ReviewStatus::Acknowledged,
        'review_period_start' => now()->subMonths(6),
        'review_period_end' => now()->subMonths(3),
    ]);

    Livewire::test(ListPerformanceReviews::class)
        ->assertCanSeeTableRecords([$draft, $acknowledged])
        ->filterTable('status', ReviewStatus::Draft->value)
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$acknowledged]);
});
