<?php

namespace App\Filament\Client\Resources\UserResource\Pages;

use App\Filament\Client\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'account_users' => Tab::make(__('Account Managers'))->icon('heroicon-o-user-group')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_client_admin', false)),
            'is_client_admin' => Tab::make(__('Administrators'))->icon('heroicon-o-users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_client_admin', true)),
        ];
    }
}
