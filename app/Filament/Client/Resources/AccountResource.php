<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\AccountResource\Pages;
use App\Filament\Client\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationBadge(): ?string
    {
        return Account::query()
            ->where('client_id', Filament::getTenant()->id)
            ->where('is_active', true)
            ->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('slug')->required(),
                Forms\Components\TextInput::make('erp_id'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query->where('is_active', true);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Account')),
                Tables\Columns\TextColumn::make('slug')->label(__('Slug')),
                Tables\Columns\TextColumn::make('erp_id')->label(__('Erp Id'))->badge(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime(),
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
            RelationManagers\UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
