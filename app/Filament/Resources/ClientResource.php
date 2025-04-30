<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Filament\Resources\ClientResource\RelationManagers\AccountsRelationManager;
use App\Models\Client;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Section::make('Bedrijfsgegevens')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->columnSpan(6)
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->columnSpan(3)
                            ->required(),
                        Forms\Components\TextInput::make('erp_id')
                            ->columnSpan(3)
                            ->readOnlyOn('edit'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query->where('is_active', true);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Bedrijfsnaam')),
                Tables\Columns\TextColumn::make('slug')->label(__('Slug'))->badge(),
                Tables\Columns\TextColumn::make('erp_id')->label(__('Erp Id')),
                Tables\Columns\TextColumn::make('accounts_count')->counts('accounts')->badge()->label(__('Accounts')),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime(),
                Tables\Columns\TextColumn::make('created_user.name')->label(__('Created By')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
