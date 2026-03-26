<?php

namespace App\Filament\Resources\HR\PerformanceReviews\Pages;

use App\Filament\Resources\HR\PerformanceReviews\PerformanceReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPerformanceReview extends EditRecord
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
