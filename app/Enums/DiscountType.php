<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum DiscountType: string implements HasColor, HasIcon, HasLabel
{
    case Percentage = 'percentage';

    case Fixed = 'fixed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Percentage => 'Percentage (%)',
            self::Fixed => 'Fixed Amount ($)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Percentage => 'info',
            self::Fixed => 'success',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Percentage => Heroicon::ReceiptPercent,
            self::Fixed => Heroicon::CurrencyDollar,
        };
    }
}
