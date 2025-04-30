<?php

namespace App\Enums\AccountPackageItemAddress;

use Filament\Support\Contracts\HasLabel;

enum AllocationTypeEnum : string implements HasLabel
{
    case REGULAR = '00';
    case RESERVE_INTERN = '99';
    case RESERVE_EXTERN = '88';
    case REBUILD = 'RB';

    public function getLabel(): ?string
    {
        return match($this) {
            self::REGULAR => __('Regulier'),
            self::RESERVE_INTERN => __('Reservepakket (Intern)'),
            self::RESERVE_EXTERN => __('Reservepakket (Extern)'),
            self::REBUILD => __('Reservepakket (Rebuild)'),
        };
    }

    public function getAllocationArrayKeys(): ?array
    {
        return match($this) {
            self::REGULAR => ['allocation', 'default'],
            self::RESERVE_INTERN =>  ['allocation', 'reserved'],
            self::RESERVE_EXTERN =>  ['allocation', 'reserved'],
            self::REBUILD => ['allocation_reserved'],
        };
    }
}
