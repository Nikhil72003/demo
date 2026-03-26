<?php

namespace App\Filament\Resources\Shop\Coupons\Tables;

use App\Enums\DiscountType;
use App\Models\Shop\Coupon;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->copyable(),

                TextColumn::make('discount_type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('discount_value')
                    ->label('Discount')
                    ->state(fn (Coupon $record): string => $record->discount_type === DiscountType::Percentage
                        ? number_format((float) $record->discount_value, 0) . '%'
                        : '$' . number_format((float) $record->discount_value, 2))
                    ->sortable(),

                TextColumn::make('usage_count')
                    ->label('Used')
                    ->state(fn (Coupon $record): string => $record->usage_limit
                        ? $record->usage_count . ' / ' . $record->usage_limit
                        : (string) $record->usage_count)
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn (Coupon $record): string => match (true) {
                        $record->expires_at === null => 'gray',
                        $record->expires_at->isPast() => 'danger',
                        $record->expires_at->diffInDays(Carbon::now()) <= 7 => 'warning',
                        default => 'success',
                    })
                    ->placeholder('No expiry'),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->options(DiscountType::class),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
