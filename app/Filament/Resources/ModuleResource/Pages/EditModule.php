<?php

namespace App\Filament\Resources\ModuleResource\Pages;

use App\Filament\Custom\Resource\Pages\EditByUserRecord;
use App\Filament\Resources\ModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModule extends EditByUserRecord
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
