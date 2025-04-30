<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\UserResource\Pages;
use App\Filament\Client\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->whereNotNUll('created_by_user')
            ->whereHas('clients', function ($query) {
                $query->where('clients.id', Filament::getTenant()->id);
            })->where('is_active', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')->hidden(),
                Forms\Components\Select::make('accounts')
                    ->multiple()
                    ->label(__('Account'))
                    ->relationship(
                        name: 'accounts',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $query->whereBelongsTo(Filament::getTenant())->where('is_active', true);
                        })
                    ->live()
                    ->hidden(fn (Get $get): bool => $get('is_client_admin'))
                    ->preload()
                    ->requiredUnless('is_client_admin', true),
                Forms\Components\TextInput::make('name')->required()->label(__('name')),
                Forms\Components\TextInput::make('email')->required()->label(__('email')),
                Forms\Components\TextInput::make('password')->password()->required()->hiddenOn('edit')->label(__('password')),
                Forms\Components\Toggle::make('is_super_admin')
                    ->when(!auth()->user()->is_super_admin, function($toggle) {
                        $toggle->hidden();
                    })->label(__('Super Admin')),
                Forms\Components\Toggle::make('is_client_admin')
                    ->disabled(fn (Get $get): bool => $get('id') === auth()->id())
                    ->when(!auth()->user()->is_super_admin, function($toggle) {
                            $toggle->hidden();
                    })
                    ->label(__('Client Admin'))
                    ->live(),
                Forms\Components\Toggle::make('is_active')
                    ->when(!auth()->user()->is_super_admin , function($toggle) {
                        $toggle->hidden();
                    })->label(__('Actief')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query
                    ->whereNotNUll('created_by_user')
                    ->whereHas('clients', function ($query) {
                        $query
                            ->where('clients.id', Filament::getTenant()->id);
                    })->where('is_active', true);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('name')),
                Tables\Columns\TextColumn::make('email')->label(__('Email')),
                Tables\Columns\TextColumn::make('accounts.name')->label(__('Accounts'))->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('Created At')),
                Tables\Columns\TextColumn::make('created_user.name')->label(__('Created By')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
