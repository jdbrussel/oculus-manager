<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNotNUll('created_by_user')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Section::make(__('Gebruiker'))
                    ->columnSpan(8)
                    ->columns(8)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(8)
                            ->required()
                            ->label(__('name')),
                        Forms\Components\TextInput::make('email')
                            ->columnSpan(8)
                            ->required()
                            ->label(__('email')),
                        Forms\Components\TextInput::make('password_new_1')
                            ->columnSpan(4)
                            ->password()
                            ->requiredWith(['password_new_2'])
                            ->hiddenOn('create')
                            ->label(__('Nieuw password')),
                        Forms\Components\TextInput::make('password_new_2')
                            ->columnSpan(4)
                            ->requiredWith(['password_new_1'])
                            ->password()
                            ->hiddenOn('create')
                            ->label(__('Herhaal password')),
                        Forms\Components\TextInput::make('password')
                            ->password()->required()
                            ->hiddenOn('edit')
                            ->label(__('password')),
                    ]),
                Forms\Components\Section::make(__('Settings'))
                    ->columnSpan(4)
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('clients')
                            ->multiple()
                            ->label(__('Bedrijven'))
                            ->columnSpan(2)
                            ->relationship('client', 'name', function ($query) {
                                $query->where('is_active', true);
                            })
                            ->preload()
                            ->requiredUnless('is_super_admin', true),
                        Forms\Components\Select::make('accounts')
                            ->multiple()
                            ->label(__('Accounts'))
                            ->columnSpan(2)
                            ->relationship('accounts', 'name', function ($query) {
                                $query->where('is_active', true);
                            })
                            ->preload()
                            ->requiredUnless('is_super_admin', true),
                        Forms\Components\Toggle::make('is_super_admin')
                            ->columnSpan(2)
                            ->default(false)
                            ->label(__('Super Admin')),
                        Forms\Components\Toggle::make('is_client_admin')
                            ->columnSpan(2)
                            ->default(false)
                            ->label(__('Client Admin')),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->hiddenOn('create')
                            ->columnSpan(2)
                            ->label(__('Actief')),

                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query->whereNotNull('created_by_user')
                    ->where('is_active', true);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email')),
                Tables\Columns\TextColumn::make('clients.name')
                    ->label(__('Bedrijven'))->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('Created at')),
                Tables\Columns\TextColumn::make('created_user.name')
                    ->label('Created By'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
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
