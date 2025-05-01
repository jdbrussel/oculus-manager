<?php

namespace App\Filament\Resources\AccountPackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountPackageSealsRelationManager extends RelationManager
{
    protected static string $relationship = 'account_package_seals';

    protected static ?string $icon = 'heroicon-o-code-bracket-square';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Sealpakketten');
    }
    public static function getBadge(Model $ownerRecord, string $pageClass): string
    {
        $count = $ownerRecord->account_package_seals->count();
        if ($count > 0) {
            return $count;
        }
        return false;
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('account_package_items')
                    ->relationship('account_package_items', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                TextColumn::make('account_package_items_count')
                    ->counts('account_package_items')
                    ->label(__('Num Items'))
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
