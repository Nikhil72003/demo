<?php

namespace App\Filament\Widgets;

use App\Enums\ReviewStatus;
use App\Models\HR\PerformanceReview;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class UpcomingReviewsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->upcomingReviewsQuery())
            ->defaultPaginationPageOption(5)
            ->heading('Upcoming & Overdue Reviews')
            ->description('Performance reviews due in the next 30 days or already overdue')
            ->emptyStateHeading('No upcoming reviews')
            ->emptyStateIcon(Heroicon::ClipboardDocumentList)
            ->defaultSort('due_date', 'asc')
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Employee')
                    ->weight(FontWeight::Medium)
                    ->searchable(),

                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->placeholder('Not assigned')
                    ->toggleable(),

                TextColumn::make('review_period')
                    ->searchable(),

                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('days_status')
                    ->label('Status')
                    ->state(function (PerformanceReview $record): string {
                        if (! $record->due_date) {
                            return 'No due date';
                        }

                        $days = (int) now()->startOfDay()->diffInDays($record->due_date->startOfDay(), false);

                        if ($days < 0) {
                            return abs($days) . ' day' . (abs($days) === 1 ? '' : 's') . ' overdue';
                        }

                        if ($days === 0) {
                            return 'Due today!';
                        }

                        return 'Due in ' . $days . ' day' . ($days === 1 ? '' : 's');
                    })
                    ->badge()
                    ->color(fn (PerformanceReview $record): string => $this->getBadgeColor($record)),

                TextColumn::make('status')
                    ->badge(),
            ]);
    }

    /** @return Builder<PerformanceReview> */
    private function upcomingReviewsQuery(): Builder
    {
        return PerformanceReview::query()
            ->whereIn('status', [ReviewStatus::Draft, ReviewStatus::Submitted])
            ->where(function (Builder $query): void {
                $query->whereNull('due_date')
                    ->orWhere('due_date', '<=', Carbon::now()->addDays(30)->endOfDay());
            })
            ->with(['employee', 'reviewer']);
    }

    private function getBadgeColor(PerformanceReview $record): string
    {
        if (! $record->due_date) {
            return 'gray';
        }

        $days = (int) now()->startOfDay()->diffInDays($record->due_date->startOfDay(), false);

        return match (true) {
            $days < 0 => 'danger',
            $days === 0 => 'warning',
            $days <= 7 => 'warning',
            default => 'info',
        };
    }
}
