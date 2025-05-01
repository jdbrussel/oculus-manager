<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\component\Connectors\Oculus\OculusSyncher;
use App\component\Connectors\Google\GoogleSheetsSyncher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountCalloffArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'calloff_articles';

    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    protected static ?string $title = 'Afroep artikelen';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->modules->contains('slug', 'stock-manager');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('erp_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('external_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('external_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('erp_id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('external_id')->label(__('External Id'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('external_name'),
                Tables\Columns\TextColumn::make('in_stock'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([

                Tables\Actions\Action::make('google_synchronize')
                    ->label(__('Synch met Google'))
                    ->color('google')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation(false)
                    ->action(function() {
                        GoogleSheetsSyncher::synchAccountCalloffArticles($this->getOwnerRecord());
                    })
                    ->visible(function() {
                        return array_key_exists('google_sheets', $this->getOwnerRecord()->config['calloff_articles']['external_synchronization']);
                    }),
                Tables\Actions\Action::make('erp_synchronize')
                    ->label(__('Synch met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountCalloffArticles($this->getOwnerRecord());
                    })
                    ->visible(function() {
                        return ($this->getOwnerRecord()->erp_status->value === '200');
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('environment', $this->getOwnerRecord()->environment)
                ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
