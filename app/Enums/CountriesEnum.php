<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CountriesEnum : string implements HasLabel
{
    case NL = 'NL';
    case BE = 'BE';

    public function getLabel() : string
    {
        return match($this) {
            self::NL => __('Nederland'),
            self::BE => __('België'),
        };
    }

    public function locales(): array
    {
        return match($this) {
            self::NL => [
                'nl_NL' => 'Nederlands'
            ],
            self::BE => [
                'nl_BE' => 'Nederlands',
                'fr_BE' => 'Francais'
            ],
        };
    }

    public function defaultLocale(): array
    {
        return match($this) {
            self::NL => ['nl_NL' => 'Nederlands'],
            self::BE => ['nl_BE' => 'Nederlands'],
        };
    }

    public function phoneExtension(): string
    {
        return match($this) {
            self::NL => '+31',
            self::BE => '+32',
        };
    }

}
