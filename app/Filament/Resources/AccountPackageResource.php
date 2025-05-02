<?php

namespace App\Filament\Resources;

use App\Enums\AccountPackage\TypeEnum;
use App\Enums\EnvironmentEnum;
use App\Enums\ModulesEnum;
use App\Filament\Resources\AccountPackageResource\Pages;
use App\Filament\Resources\AccountPackageResource\RelationManagers;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageOverview;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageWidget;
use App\Models\Account;
use App\Models\AccountPackage;
use App\Models\Module;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccountPackageResource extends Resource
{
    protected static ?string $model = AccountPackage::class;


    public static ?string $moduleSlug = 'package-manager';

    public static function module() {
        if(self::$moduleSlug) {
            return Module::where('slug', self::$moduleSlug)->first();
        }
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        if(self::$moduleSlug) {
            return ModulesEnum::from(self::$moduleSlug)->getLabel();
        }
        return null;
    }

    protected static ?int $navigationSort = 1;
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
                TextColumn::make('erp_id')
                    ->label(__('erp_id'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.slug')
                    ->label(__('account'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('environment')
                    ->label(__('environment'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('full_name')->label(__('Omschrijving')),
                TextColumn::make('scheduled_delivery_datetime')
                    ->dateTime('d-m-Y H:i')->suffix(' uur')
                    ->label(__('Delivery Date')),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                TextColumn::make('account_package_items_count')
                    ->badge()
                    ->counts('account_package_items')
                    ->label(__('Aantal onderdelen')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_id')
                    ->label(__('Account'))
                    ->options(
                        Account::whereRelation('modules', 'slug', '=', 'package-manager')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('environment')
                    ->label(__('Environment'))
                    ->options(
                        EnvironmentEnum::class
                    )
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make()
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])->modifyQueryUsing(function ($query) {
                $query->whereRelation('account.modules', 'slug', '=', self::$moduleSlug);
                return $query;
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AccountPackageItemsRelationManager::class,
            RelationManagers\AccountPackageSealsRelationManager::class,
            RelationManagers\AccountPackageBoxesRelationManager::class,
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
