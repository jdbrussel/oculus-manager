<?php

namespace App\Filament\Custom\Tenancy;

use App\Models\Seller;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterNewSeller extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('Register New Seller');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(12)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('sellers name'))
                            ->columnSpan(12),
                        TextInput::make('slug')
                            ->label(__('sellers slug'))
                            ->columnSpan(12),
                        ToggleButtons::make('type')
                            ->label(__('sellers type'))
                            ->columnSpan(12)
                            ->inline()
                            ->options([
                                'business' => __('seller is business'),
                                'individual' => __('seller is indiviual'),
                            ])->default('individual'),
                    ]),
            ]);
    }

    protected function handleRegistration(array $data): Seller
    {
        $seller = Seller::create($data);

        $seller->users()->attach(auth()->user());

        return $seller;
    }
}
