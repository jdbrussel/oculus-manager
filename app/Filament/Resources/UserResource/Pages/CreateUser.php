<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Custom\Resource\Pages\CreatedByUserRecord;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreatedByUserRecord
{
    protected static string $resource = UserResource::class;
}
