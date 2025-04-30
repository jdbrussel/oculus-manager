<?php

namespace App\Filament\Client\Resources\AccountResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

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
                    ->hiddenOn('edit')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query) {
                $query->whereNotNull('created_by_user');
            })
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('name')),
                Tables\Columns\TextColumn::make('email')->label(__('email')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['created_by_user'] = auth()->id();
                    return $data;
                })->after(function (Mixed $record) {
                        Filament::getTenant()->users()->attach($record);
                        return true;
                    }),
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $query
                        ->whereNotNull('created_by_user')
                        ->whereHas('clients', function ($query) {
                            $query->where('clients.id', Filament::getTenant()->id);
                        });
                        return $query;
                    })->preloadRecordSelect()
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

}
