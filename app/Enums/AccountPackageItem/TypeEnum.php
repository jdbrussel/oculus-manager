<?php

namespace App\Enums\AccountPackageItem;
use Filament\Support\Contracts\HasLabel;

enum TypeEnum : string implements HasLabel
{
    case ITEM = 'item';
    case SET = 'set';
    case BUNDLE = 'bundle';
    case SHOP_SPECIFIC_SET = 'shop_specific';

    public function getLabel() : string
    {
        return match($this) {
            self::ITEM => __('Los Item'),
            self::SET => __('Set'),
            self::BUNDLE => __('Bundel'),
            self::SHOP_SPECIFIC_SET => __('Winkelspecifiek')
        };
    }

    public function getDescription($num_versions = false, $num_per_version = false) : string
    {
        return match($this) {
            self::ITEM => __('Losse Item'),
            self::SET => __('Sets van :set_size (:num_versions/:num_per_version)', [
                'set_size' => ($num_versions * $num_per_version),
                'num_versions' => ($num_versions ?? 1),
                'num_per_version' => ($num_per_version ?? 1)
            ]),
            self::BUNDLE => __('Bundels van :bundle_size', [
                'bundle_size' => ($num_versions * $num_per_version)
            ]),
            self::SHOP_SPECIFIC_SET => __('Winkelspecifieke sets')
        };
    }

}
