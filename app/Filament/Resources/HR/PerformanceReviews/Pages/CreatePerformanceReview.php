<?php

namespace App\Filament\Resources\HR\PerformanceReviews\Pages;

use App\Filament\Resources\HR\PerformanceReviews\PerformanceReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerformanceReview extends CreateRecord
{
    protected static string $resource = PerformanceReviewResource::class;
}
