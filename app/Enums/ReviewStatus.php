<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ReviewStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';

    case Submitted = 'submitted';

    case Acknowledged = 'acknowledged';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Acknowledged => 'Acknowledged',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Submitted => 'warning',
            self::Acknowledged => 'success',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Draft => Heroicon::PencilSquare,
            self::Submitted => Heroicon::Clock,
            self::Acknowledged => Heroicon::CheckBadge,
        };
    }
}
