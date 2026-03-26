<?php

namespace App\Filament\Resources\HR\PerformanceReviews\Tables;

use App\Enums\ReviewRating;
use App\Enums\ReviewStatus;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PerformanceReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('reviewer.name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not assigned')
                    ->toggleable(),

                TextColumn::make('review_period')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('review_period_start')
                    ->label('Period Start')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('review_period_end')
                    ->label('Period End')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date?->isPast() && $record->status !== ReviewStatus::Acknowledged ? 'danger' : null),

                TextColumn::make('rating')
                    ->badge()
                    ->placeholder('Not rated'),

                SelectColumn::make('status')
                    ->options(ReviewStatus::class)
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ReviewStatus::class),

                SelectFilter::make('rating')
                    ->options(ReviewRating::class),
            ])
            ->defaultSort('due_date', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
