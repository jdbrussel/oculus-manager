<?php
namespace App\Filament\Client\Pages\Tenancy;

use App\Models\Client;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterClient extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Client';
    }

    public function form(Form $form): Form
    {
    return $form
        ->schema([
            TextInput::make('name'),
            TextInput::make('erp_id'),
            TextInput::make('slug'),
        ]);
    }

    protected function handleRegistration(array $data): Client
    {
        $data['created_by_user'] = auth()->id();
        $client = Client::create($data);
        $client->users()->attach(auth()->user());
        return $client;
    }
}
