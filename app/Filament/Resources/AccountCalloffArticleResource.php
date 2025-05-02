<?php

namespace App\Filament\Resources;

use App\Enums\EnvironmentEnum;
use App\Enums\ModulesEnum;
use App\Filament\Resources\AccountCalloffArticleResource\Pages;
use App\Filament\Resources\AccountCalloffArticleResource\RelationManagers;
use App\Models\Account;
use App\Models\AccountCalloffArticle;
use App\Models\Module;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountCalloffArticleResource extends Resource
{
    protected static ?string $model = AccountCalloffArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $moduleSlug = 'stock-manager';

    public static function module() {
        if(self::$moduleSlug) {
            return Module::where('slug', self::$moduleSlug)->first();
        }
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        if(self::module()) {
            return ModulesEnum::from(self::module()->slug)->getLabel();
        }
        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                Tables\Columns\TextColumn::make('erp_id'),
                Tables\Columns\TextColumn::make('external_id')->label(__('External Id'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('account.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('environment')->badge()->sortable(),

//                Tables\Columns\TextColumn::make('external_name'),
                Tables\Columns\TextColumn::make('in_stock'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function ($query) {
                $query->whereRelation('account.modules', 'slug', '=', self::$moduleSlug);
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
            'index' => Pages\ListAccountCalloffArticles::route('/'),
            'create' => Pages\CreateAccountCalloffArticle::route('/create'),
            'edit' => Pages\EditAccountCalloffArticle::route('/{record}/edit'),
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
