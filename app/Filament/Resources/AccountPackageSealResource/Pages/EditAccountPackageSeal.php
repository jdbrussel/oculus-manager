<?php

namespace App\Filament\Resources\AccountPackageSealResource\Pages;

use App\Filament\Resources\AccountPackageSealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountPackageSeal extends EditRecord
{
    protected static string $resource = AccountPackageSealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
