<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\component\Connectors\Oculus\OculusSyncher;
use App\Filament\Custom\Resource\Pages\CreatedByUserRecord;
use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreatedByUserRecord
{
    protected static string $resource = AccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = OculusSyncher::synchAccountData($data);
        return parent::mutateFormDataBeforeCreate($data);
    }
}

