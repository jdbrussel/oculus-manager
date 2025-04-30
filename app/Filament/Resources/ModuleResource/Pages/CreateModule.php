<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Custom\Resource\Pages\CreatedByUserRecord;
use App\Filament\Resources\ModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateModule extends CreatedByUserRecord
{
    protected static string $resource = ModuleResource::class;
}
