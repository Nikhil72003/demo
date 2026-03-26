<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum ReviewRating: string implements HasColor, HasIcon, HasLabel
{
    case Outstanding = 'outstanding';

    case ExceedsExpectations = 'exceeds_expectations';

    case MeetsExpectations = 'meets_expectations';

    case NeedsImprovement = 'needs_improvement';

    case Unsatisfactory = 'unsatisfactory';

    public function getLabel(): string
    {
        return match ($this) {
            self::Outstanding => 'Outstanding',
            self::ExceedsExpectations => 'Exceeds Expectations',
            self::MeetsExpectations => 'Meets Expectations',
            self::NeedsImprovement => 'Needs Improvement',
            self::Unsatisfactory => 'Unsatisfactory',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Outstanding => 'success',
            self::ExceedsExpectations => 'info',
            self::MeetsExpectations => 'primary',
            self::NeedsImprovement => 'warning',
            self::Unsatisfactory => 'danger',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Outstanding => Heroicon::Star,
            self::ExceedsExpectations => Heroicon::ArrowTrendingUp,
            self::MeetsExpectations => Heroicon::CheckCircle,
            self::NeedsImprovement => Heroicon::ExclamationCircle,
            self::Unsatisfactory => Heroicon::XCircle,
        };
    }
}
