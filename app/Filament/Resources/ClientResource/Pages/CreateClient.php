<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Custom\Resource\Pages\CreatedByUserRecord;
use App\Filament\Resources\ClientResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreatedByUserRecord
{
    protected static string $resource = ClientResource::class;

}
