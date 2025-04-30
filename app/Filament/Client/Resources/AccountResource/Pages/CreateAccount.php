<?php

namespace App\Filament\Client\Resources\AccountResource\Pages;

use App\Filament\Client\Resources\AccountResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = Filament::getTenant()->id;
        $data['created_by_user'] = auth()->id();
        return $data;
    }
}
