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
use Filament\Schemas\Schema;

class PerformanceReviewForm
{
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
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        ToggleButtons::make('rating')
                            ->options(ReviewRating::class)
                            ->inline(),

                        Textarea::make('goals_achieved')
                            ->label('Goals Achieved')
                            ->rows(4)
                            ->maxLength(65535),

                        Textarea::make('manager_comments')
                            ->label('Manager Comments')
                            ->rows(4)
                            ->maxLength(65535),

                        Textarea::make('employee_comments')
                            ->label('Employee Comments')
                            ->rows(4)
                            ->maxLength(65535),
                    ]),
            ]);
    }
}
