<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name')),
                Tables\Columns\TextColumn::make('slug')->label(__('Slug')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->icon('heroicon-o-plus')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    detachBulkAction::make()
                ]),
            ]);
    }
}
