<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user'] = auth()->id();
        return $data;
    }

    protected function afterRecordCreated(mixed $record, array $data) {
        Filament::getTenant()->users()->attach($record);
        return redirect()->route('client.users.edit', $record);
    }
}
