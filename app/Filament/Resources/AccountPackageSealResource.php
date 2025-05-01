<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountPackageSealResource\Pages;
use App\Filament\Resources\AccountPackageSealResource\RelationManagers;
use App\Models\Account;
use App\Models\AccountPackage;
use App\Models\AccountPackageItem;
use App\Models\AccountPackageSeal;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class AccountPackageSealResource extends Resource
{
    protected static ?string $model = AccountPackageSeal::class;
    protected static ?string $navigationGroup = 'Packages';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('account_id')
                    ->options(Account::all()->pluck('name', 'id'))
                    ->live()
                    ->preload()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('account_package_id', null))
                    ->required()
                    ->hiddenOn('edit'),
                Forms\Components\Select::make('account_package_id')
                    ->options(fn (Forms\Get $get) : Collection => AccountPackage::query()
                        ->where('account_id', $get('account_id'))
                        ->pluck('erp_id', 'id')
                    )
                    ->searchable()
                    ->live()
                    ->preload()
                    ->required()
                    ->hiddenOn('edit'),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('erp_id'),
                Forms\Components\TextInput::make('external_id'),
                Forms\Components\TextInput::make('external_name'),
                Select::make('account_package_items')
                    ->relationship('account_package_items', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('account_package.erp_id'),
                TextColumn::make('account_package.account.name')->badge(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AccountPackageItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountPackageSeals::route('/'),
            'create' => Pages\CreateAccountPackageSeal::route('/create'),
            'edit' => Pages\EditAccountPackageSeal::route('/{record}/edit'),
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
