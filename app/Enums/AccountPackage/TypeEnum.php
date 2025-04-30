<?php

namespace App\Enums\AccountPackage;

use Filament\Support\Contracts\HasLabel;

enum TypeEnum : string implements HasLabel
{
    case Day = 'D';
    case Week = 'W';
    case Theme = 'T';

    public function getLabel() : string
    {
        return match($this) {
            self::Day => __('Dagpakket'),
            self::Week => __('Weekpakket'),
            self::Theme => __('Themapakket'),
        };
    }
    public function getAllocationTypes() : array
    {
        return match($this) {
            self::Day => [ 'direct', 'dc' ],
            self::Week => [ 'direct', 'dc' ],
            self::Theme => [ 'direct', 'dc', 'dc-list' ],
        };
    }

}
