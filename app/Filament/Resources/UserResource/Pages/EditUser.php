<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Custom\Resource\Pages\EditByUserRecord;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditByUserRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if(!empty($data['password_new_1']) && !empty($data['password_new_2'])) {
            if($data['password_new_1'] == $data['password_new_2']) {
                $data['password'] = bcrypt($data['password_new_1']);
            }
        }
        return parent::handleRecordUpdate($record, $data);
    }

//    protected function getRedirectUrl(): ?string
//    {
//        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
//    }


}
