<?php

namespace App\Filament\Resources;

use App\Enums\Account\ErpStatusEnum;
use App\Enums\EnvironmentEnum;
use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Google\Service\Calendar\Colors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Section::make(__('ERP Settings'))
                    ->columns(12)
                    ->columnSpan(12)
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->columnSpan(3)
                            ->relationship('client', 'name', function ($query) {
                                $query->where('is_active', true);
                            })
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('erp_id')
                            ->columnSpan(2)
                            ->default('913508')
                            ->live()
                            ->requiredWith('environment'),
                        Forms\Components\Select::make('erp_status')
                            ->label(__('Erp Status'))
                            ->options(ErpStatusEnum::class)
                            ->columnSpan(2)
                            ->disabled(),
                        Forms\Components\ToggleButtons::make('environment')
                            ->columnSpan(4)
                            ->label(__('Erp omgeving'))
                            ->options(EnvironmentEnum::class)
                            ->icons(EnvironmentEnum::icons())
                            ->inline()
                            ->live()
                            ->requiredWith('erp_id'),

                    ]),
                Forms\Components\Section::make(__('Bedrijfsnaam'))
                    ->hiddenOn('create')
                    ->columnSpan(12)
                    ->columns(12)
                    ->schema([
                    Forms\Components\TextInput::make('name')
                        ->columnSpan(6)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->columnSpan(3)
                        ->required(),
                ]),
                Forms\Components\Section::make(__('Modules'))
                    ->columnSpan(12)
                    ->columns(12)
                    ->schema([
                        Forms\Components\Select::make('modules')
                            ->label(false)
                            ->multiple()
                            ->preload()
                            ->relationship('modules', 'name')
                            ->columnSpan(12)
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query->whereRelation('client', 'is_active', true);
            })
            ->columns([
                BadgeableColumn::make('name')
                    ->label(__('Bedrijfsnaam'))
                    ->sortable()
                    ->prefixBadges([
                        Badge::make('slug')
                            ->label(fn($record) => strtoupper($record->slug))
                            ->visible(fn($record) => !empty($record->slug)),
                    ]),
                BadgeableColumn::make('erp_id')
                    ->asPills()
                    ->separator('')
                    ->label(__('Erp Id'))
                    ->prefixBadges([
                        Badge::make('environment')
                            ->label('Dev')
                            ->color('success')
                            ->visible(fn($record) => $record->environment->name === 'development'),
                    ]),
                TextColumn::make('erp_status.name')
                    ->badge()
                    ->label(__('ERP Status')),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Bedrijfsnaam'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->badge()
                    ->label(__('users')),
                Tables\Columns\TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label(__('Modules'))
                    ->badge()
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
            RelationManagers\AccountUsersRelationManager::class,
            RelationManagers\AccountContactsRelationManager::class,
            RelationManagers\AccountAddressesRelationManager::class,
            RelationManagers\AccountCalloffArticlesRelationManager::class,
            RelationManagers\AccountPackagesRelationManager::class,

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
