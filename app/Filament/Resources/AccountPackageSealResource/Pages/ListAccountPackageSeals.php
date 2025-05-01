<?php

namespace App\Filament\Resources\AccountPackageSealResource\Pages;

use App\Filament\Resources\AccountPackageSealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountPackageSeals extends ListRecords
{
    protected static string $resource = AccountPackageSealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
