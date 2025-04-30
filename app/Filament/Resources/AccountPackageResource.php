<?php

namespace App\Filament\Resources;

use App\Enums\AccountPackage\TypeEnum;
use App\Enums\EnvironmentEnum;
use App\Filament\Resources\AccountPackageResource\Pages;
use App\Filament\Resources\AccountPackageResource\RelationManagers;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageOverview;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageWidget;
use App\Models\Account;
use App\Models\AccountPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccountPackageResource extends Resource
{
    protected static ?string $model = AccountPackage::class;
    protected static ?string $navigationParentItem = 'Accounts';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getWidgets(): array
    {
        return [
            AccountPackageWidget::class,
        ];
    }

    public static function form(Form $form): Form
    {

        return $form
            ->columns(12)
            ->schema([
                Forms\Components\Section::make(__('Oculus'))
                ->columns(12)
                ->schema([
                    Forms\Components\TextInput::make('erp_id')
                        ->columnSpan(3)
                        ->disabledOn('edit')
                        ->required(),
                    Forms\Components\Select::make('environment')
                        ->columnSpan(9)
                        ->options(EnvironmentEnum::class)
                        ->disabledOn('edit')
                        ->required(),
                    Forms\Components\TextInput::make('edition')
                        ->columnSpan(3)
                        ->disabledOn('edit')
                        ->required(),
                    Forms\Components\TextInput::make('year')
                        ->columnSpan(2)
                        ->disabledOn('edit')
                        ->required(),
                    Forms\Components\Select::make('type')
                        ->columnSpan(2)
                        ->options(TypeEnum::class)
                        ->disabledOn('edit')
                        ->required(),
                    Forms\Components\Select::make('account_id')
                        ->label(__('Account'))
                        ->columnSpan(5)
                        ->options(Account::all()->pluck('name', 'id'))
                        ->disabledOn('edit')
                        ->required(),

                ]),
            Forms\Components\Section::make($form->getRecord()->account->name)
                ->columns(12)
                ->schema([
                    Forms\Components\TextInput::make('external_name')
                        ->columnSpan(4)
                        ->required(),
                    Forms\Components\TextInput::make('external_id')
                        ->columnSpan(2),
                ]),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('erp_id')->label(__('erp_id'))->searchable()->sortable(),
                TextColumn::make('account.name')->label(__('account'))->sortable(),
                TextColumn::make('full_name')->label(__('Omschrijving')),
                TextColumn::make('account_package_items_count')
                    ->badge()
                    ->counts('account_package_items')
                    ->label(__('Aantal onderdelen')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])->modifyQueryUsing(function ($query) {
                return $query->whereRelation('account', 'deleted_at', null);
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AccountPackageItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountPackages::route('/'),
            'create' => Pages\CreateAccountPackage::route('/create'),
            'edit' => Pages\EditAccountPackage::route('/{record}/edit'),
        ];
    }

}
