<?php

namespace App\Filament\Custom\Resource\Pages;

use Filament\Resources\Pages\EditRecord;

class EditByUserRecord extends EditRecord
{
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user'] = auth()->id();
        return $data;
    }
}
