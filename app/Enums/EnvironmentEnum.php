<?php

namespace App\Enums;

use Couchbase\GetOptions;
use Filament\Support\Contracts\HasLabel;

enum EnvironmentEnum: string implements HasLabel
{
    case PROD = 'production';
    case DEV = 'development';

    public function getLabel() : string
    {
        return match($this) {
            self::PROD => __('Production'),
            self::DEV => __('Development'),
        };
    }

    public function getIcon() : string
    {
        return match($this) {
            self::PROD => 'heroicon-o-globe-alt',
            self::DEV => 'heroicon-o-beaker',
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
