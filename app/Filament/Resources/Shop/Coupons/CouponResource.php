<?php

namespace App\Filament\Resources\Shop\Coupons;

use App\Filament\Resources\Shop\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Shop\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Shop\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Shop\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Shop\Coupons\Tables\CouponsTable;
use App\Models\Shop\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string | UnitEnum | null $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'shop/coupons';

    public static function form(Schema $schema): Schema
    {
        return CouponForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
