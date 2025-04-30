<?php

namespace App\Filament\Resources\AccountPackageResource\RelationManagers;

use App\component\Connectors\Oculus\OculusSyncher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Novadaemon\FilamentPrettyJson\Form\PrettyJsonField;

class AccountPackageItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'account_package_items';

    protected static ?string $icon = 'heroicon-o-square-2-stack';

    protected static ?string $title = 'Pakketonderdelen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                PrettyJsonField::make('allocation')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('erp_id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('external_id'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('synched_at')->dateTime(),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('erp_synchronize')
                    ->label(__('Synchroniseren met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountPackageItems($this->getOwnerRecord());
                    })
                    ->visible(function() {
                        return true;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
