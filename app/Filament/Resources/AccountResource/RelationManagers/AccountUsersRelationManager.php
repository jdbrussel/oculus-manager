<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\component\Connectors\Oculus\OculusSyncher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';


    protected static ?string $icon = 'heroicon-o-users';
    protected static ?string $title = 'Gebruikers';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('synched_at')->label(__('Laatste synchronisatie'))->dateTime(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Nieuwe gebruiker'))
                    ->icon('heroicon-o-plus'),
                Tables\Actions\Action::make('erp_account_users')
                    ->label(__('Synchroniseren met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountUsers($this->getOwnerRecord());
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
