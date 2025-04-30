<?php

namespace App\Filament\Custom\Resource;

use Filament\Resources\Resource;

class SuperAdminResource extends Resource
{
    protected static bool $isScopedToTenant = false;
    public static function canViewAny(): bool
    {
        return auth()->user()->is_super_admin && auth()->user()->is_active;
    }

}
