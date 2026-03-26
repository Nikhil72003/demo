<?php

namespace App\Filament\Resources\HR\PerformanceReviews\Schemas;

use App\Enums\ReviewRating;
use App\Enums\ReviewStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PerformanceReviewForm
{
    private static function isSubmittedOrAcknowledged(mixed $status): bool
    {
        return static::isStatus($status, ReviewStatus::Submitted)
            || static::isStatus($status, ReviewStatus::Acknowledged);
    }

    private static function isStatus(mixed $status, ReviewStatus $expected): bool
    {
        if ($status instanceof ReviewStatus) {
            return $status === $expected;
        }

        return ReviewStatus::tryFrom((string) $status) === $expected;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Period')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('reviewer_id')
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Reviewer / Manager'),

                        TextInput::make('review_period')
                            ->required()
                            ->placeholder('e.g. Q1 2026, Annual 2025')
                            ->maxLength(100),

                        ToggleButtons::make('status')
                            ->options(ReviewStatus::class)
                            ->inline()
                            ->required()
                            ->default(ReviewStatus::Draft)
                            ->live()
                            ->columnSpanFull(),

                        DatePicker::make('review_period_start')
                            ->required()
                            ->label('Period Start'),

                        DatePicker::make('review_period_end')
                            ->required()
                            ->label('Period End'),

                        DatePicker::make('due_date')
                            ->label('Due Date'),
                    ]),

                Section::make('Evaluation')
                    ->description(fn (Get $get): ?string => static::isSubmittedOrAcknowledged($get('status'))
                        ? 'Rating and manager comments are required before submitting.'
                        : null)
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        ToggleButtons::make('rating')
                            ->options(ReviewRating::class)
                            ->inline()
                            ->required(fn (Get $get): bool => static::isSubmittedOrAcknowledged($get('status'))),

                        Textarea::make('goals_achieved')
                            ->label('Goals Achieved')
                            ->rows(4)
                            ->maxLength(65535),

                        Textarea::make('manager_comments')
                            ->label('Manager Comments')
                            ->rows(4)
                            ->maxLength(65535)
                            ->required(fn (Get $get): bool => static::isSubmittedOrAcknowledged($get('status'))),

                        Textarea::make('employee_comments')
                            ->label('Employee Comments')
                            ->helperText('Filled in by the employee when acknowledging the review.')
                            ->rows(4)
                            ->maxLength(65535)
                            ->required(fn (Get $get): bool => static::isStatus($get('status'), ReviewStatus::Acknowledged)),
                    ]),
            ]);
    }
}
