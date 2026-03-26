<?php

namespace App\Filament\Resources\Shop\Coupons\Schemas;

use App\Enums\DiscountType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Details')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('description')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        ToggleButtons::make('discount_type')
                            ->options(DiscountType::class)
                            ->inline()
                            ->required()
                            ->live()
                            ->default(DiscountType::Percentage)
                            ->columnSpanFull(),

                        TextInput::make('discount_value')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn (Get $get): string => $get('discount_type') === DiscountType::Percentage->value ? '%' : '$'),

                        TextInput::make('maximum_discount')
                            ->label('Maximum Discount ($)')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (Get $get): bool => $get('discount_type') === DiscountType::Percentage->value)
                            ->placeholder('No limit'),

                        TextInput::make('minimum_order_value')
                            ->label('Minimum Order Value ($)')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('No minimum'),

                        TextInput::make('usage_limit')
                            ->label('Usage Limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Unlimited'),
                    ]),

                Section::make('Validity')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpanFull(),

                        DatePicker::make('starts_at')
                            ->label('Start Date'),

                        DatePicker::make('expires_at')
                            ->label('Expiry Date')
                            ->minDate(fn (Get $get) => $get('starts_at')),
                    ]),
            ]);
    }
}
