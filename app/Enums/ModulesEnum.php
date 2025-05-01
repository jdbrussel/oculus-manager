<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ModulesEnum: string implements HasLabel
{
    case PACKAGEMANAGER = 'package-manager';
    case STOCKMANAGER = 'stock-manager';

    public function getLabel() : string
    {
        return match($this) {
            self::PACKAGEMANAGER => __('Package Manager'),
            self::STOCKMANAGER => __('Stock Manager'),
        };
    }

    public function getIcon() : string
    {
        return match($this) {
            self::PACKAGEMANAGER => 'heroicon-o-globe-alt',
            self::STOCKMANAGER => 'heroicon-o-beaker',
        };
    }

    public static function icons() : array
    {
        $icons = [];
        foreach (self::cases() as $case) {
            $icons[$case->value] =  self::from($case->value)->getIcon();
        };
        return $icons;
    }

}
