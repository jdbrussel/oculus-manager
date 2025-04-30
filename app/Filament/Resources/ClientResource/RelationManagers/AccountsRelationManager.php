<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Account;
use App\Models\AccountPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                    ->label(__('name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label(__('slug'))
                    ->required()
                    ->maxLength(32)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Bedrijfsnaam'))->sortable(),
                Tables\Columns\TextColumn::make('slug')->label(__('Slug')),
                Tables\Columns\TextColumn::make('erp_id')->label(__('Erp Id'))->badge(),
//                Tables\Columns\TextColumn::make('client.name')->label(__('Bedrijfsnaam'))->badge()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime(),
                Tables\Columns\TextColumn::make('created_user.name')->label(__('Created By')),
                Tables\Columns\TextColumn::make('modules_count')->counts('modules')->label(__('Num Modules'))->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Account $record): string => route('filament.admin.resources.accounts.edit', [
                        'tenant' => filament()->getTenant(),
                        'record' => $record
                    ])),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }


}
