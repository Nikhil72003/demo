<?php

namespace App\Filament\Resources\HR\PerformanceReviews\Pages;

use App\Enums\ReviewStatus;
use App\Filament\Resources\HR\PerformanceReviews\PerformanceReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListPerformanceReviews extends ListRecords
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'draft' => Tab::make('Draft')
                ->query(fn ($query) => $query->where('status', ReviewStatus::Draft)),
            'submitted' => Tab::make('Submitted')
                ->query(fn ($query) => $query->where('status', ReviewStatus::Submitted)),
            'acknowledged' => Tab::make('Acknowledged')
                ->query(fn ($query) => $query->where('status', ReviewStatus::Acknowledged)),
        ];
    }
}
