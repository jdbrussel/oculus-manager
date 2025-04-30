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

class AccountContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'account_contacts';

    protected static ?string $icon = 'heroicon-o-identification';

    protected static ?string $title = 'Contactpersonen';

    protected static ?string $badge = '';

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
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('department')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('function')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('erp_id')->label(__('Erp Id'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label(__('Contactpersoon'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('Email'))->searchable()->sortable(),

                Tables\Columns\TextColumn::make('department')->label(__('Department'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('function')->label(__('Function'))->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Nieuw Contactpersoon'))
                    ->icon('heroicon-o-plus'),
                Tables\Actions\Action::make('erp_account_users')
                    ->label(__('Synchroniseren met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountContacts($this->getOwnerRecord());
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
