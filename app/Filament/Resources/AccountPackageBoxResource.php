<?php

namespace App\Filament\Resources;

use App\Enums\ModulesEnum;
use App\Filament\Resources\AccountPackageBoxResource\Pages;
use App\Filament\Resources\AccountPackageBoxResource\RelationManagers;
use App\Models\Account;
use App\Models\AccountPackage;
use App\Models\AccountPackageBox;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class AccountPackageBoxResource extends Resource
{
    protected static ?string $model = AccountPackageBox::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $module = 'package-manager';

    public static function getNavigationGroup(): ?string
    {
        if(self::$module) {
            return ModulesEnum::from(self::$module)->getLabel();
        }
        return null;
    }
    protected static ?int $navigationSort = 2;

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
                Forms\Components\TextInput::make('external_name')
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
            ])->modifyQueryUsing(function ($query) {
                $query->whereRelation('account_package.account.modules', 'slug', '=', self::$module);
                return $query;
            });
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
            'index' => Pages\ListAccountPackageBoxes::route('/'),
            'create' => Pages\CreateAccountPackageBox::route('/create'),
            'edit' => Pages\EditAccountPackageBox::route('/{record}/edit'),
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
