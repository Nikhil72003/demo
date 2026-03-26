<?php

namespace App\Filament\Resources\HR\PerformanceReviews;

use App\Enums\ReviewStatus;
use App\Filament\Resources\HR\PerformanceReviews\Pages\CreatePerformanceReview;
use App\Filament\Resources\HR\PerformanceReviews\Pages\EditPerformanceReview;
use App\Filament\Resources\HR\PerformanceReviews\Pages\ListPerformanceReviews;
use App\Filament\Resources\HR\PerformanceReviews\Schemas\PerformanceReviewForm;
use App\Filament\Resources\HR\PerformanceReviews\Tables\PerformanceReviewsTable;
use App\Models\HR\PerformanceReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string | UnitEnum | null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'hr/performance-reviews';

    public static function form(Schema $schema): Schema
    {
        return PerformanceReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerformanceReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPerformanceReviews::route('/'),
            'create' => CreatePerformanceReview::route('/create'),
            'edit' => EditPerformanceReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', ReviewStatus::Submitted)->count() ?: null;
    }
}
