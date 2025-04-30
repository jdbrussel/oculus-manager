<?php

namespace App\Filament\Resources\AccountPackageResource\Pages;

use App\Filament\Resources\AccountPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountPackages extends ListRecords
{
    protected static string $resource = AccountPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
