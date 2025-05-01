<?php

namespace App\Filament\Resources\AccountPackageBoxResource\Pages;

use App\Filament\Resources\AccountPackageBoxResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountPackageBox extends EditRecord
{
    protected static string $resource = AccountPackageBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
