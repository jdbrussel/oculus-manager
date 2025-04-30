<?php

namespace App\Enums\Account;

use Filament\Support\Contracts\HasLabel;

enum ErpStatusEnum : string implements HasLabel
{
    case OK = '200';
    case UNAUTHORIZED = '401';
    case NOT_FOUND = '404';

    case DOWN = '300';

    public function getLabel() : string
    {
        return match($this) {
            self::OK => __('Up'),
            self::UNAUTHORIZED => __('Unauthorized'),
            self::NOT_FOUND => __('Not Found'),
            self::DOWN => __('Down'),
        };
    }

    public function getIcon() : string
    {
        return match($this) {
            self::OK => 'heroicon-s-check',
            self::UNAUTHORIZED => 'heroicon-o-alert',
            self::NOT_FOUND => 'heroicon-s-x',
            self::DOWN => 'heroicon-s-square-o',
        };
    }


}
