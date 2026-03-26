<?php

namespace App\Filament\Widgets;

use App\Enums\DiscountType;
use App\Models\Shop\Coupon;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class CouponSummaryWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Coupon::query()
                    ->where('is_active', true)
                    ->where(function ($query): void {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', Carbon::today());
                    })
                    ->orderByRaw('expires_at IS NULL ASC')
                    ->orderBy('expires_at')
            )
            ->defaultPaginationPageOption(5)
            ->heading('Active Coupons')
            ->description('Expiring soonest first — active coupons available for use')
            ->emptyStateHeading('No active coupons')
            ->emptyStateIcon(Heroicon::Ticket)
            ->columns([
                TextColumn::make('code')
                    ->weight(FontWeight::Medium)
                    ->searchable()
                    ->copyable(),

                TextColumn::make('discount')
                    ->label('Discount')
                    ->state(fn (Coupon $record): string => $record->discount_type === DiscountType::Percentage
                        ? number_format((float) $record->discount_value, 0) . '%'
                        : '$' . number_format((float) $record->discount_value, 2))
                    ->badge()
                    ->color('success'),

                TextColumn::make('usage_count')
                    ->label('Times Used')
                    ->state(fn (Coupon $record): string => $record->usage_limit
                        ? $record->usage_count . ' / ' . $record->usage_limit
                        : (string) $record->usage_count)
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->badge()
                    ->color(fn (Coupon $record): string => match (true) {
                        $record->expires_at === null => 'gray',
                        $record->expires_at->lte(Carbon::now()->addDays(7)) => 'danger',
                        $record->expires_at->lte(Carbon::now()->addDays(30)) => 'warning',
                        default => 'success',
                    })
                    ->placeholder('No expiry'),
            ]);
    }
}
