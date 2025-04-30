<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Custom\Resource\Pages\EditByUserRecord;
use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditByUserRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user'] = auth()->id();
        return $data;
    }
}
