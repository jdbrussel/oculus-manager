<?php

namespace App\Filament\Custom\Resource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreatedByUserRecord extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user'] = auth()->id();
        return $data;
    }
}
