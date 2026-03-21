<?php

namespace App\Filament\Widgets;

use App\Models\Shop\Product;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('qty', '<', 10)
                    ->with('productCategories')
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('qty', 'asc')
            ->heading('Low Stock Alert')
            ->description('Products with fewer than 10 units in stock')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('qty')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record): string => $record->qty === 0 ? 'danger' : 'warning'),
                TextColumn::make('productCategories.name')
                    ->label('Category')
                    ->badge()
                    ->separator(','),
            ]);
    }
}
