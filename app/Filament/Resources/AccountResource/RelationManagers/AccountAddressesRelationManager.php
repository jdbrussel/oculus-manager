<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\component\Connectors\Oculus\OculusSyncher;
use App\Enums\CountriesEnum;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'account_addresses';
    protected static ?string $icon = 'heroicon-o-truck';
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Afleveradressen');
    }
    public static function getBadge(Model $ownerRecord, string $pageClass): string
    {
        $count = $ownerRecord->account_addresses
            ->where('environment', $ownerRecord->environment)
            ->count();
        if ($count > 0) {
            return $count;
        }
        return '';
    }


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
                Tables\Columns\TextColumn::make('external_id')
                    ->label(__('Filiaalnummer'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_type')
                    ->label(__('Type Address'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Naam Filiaal'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label(__('Land'))
                    ->sortable()
                    ->searchable(),
//                Tables\Columns\TextColumn::make('erp_id')
//                    ->label(__('Oculus nummer'))
//                    ->sortable()
//                    ->searchable(),
                Tables\Columns\TextColumn::make('synched_at')
                    ->label(__('laatste synchronisatie'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('address_type')
                    ->label(__('Type adres'))
//                ->multiple()
                    ->options(
                        [
                            'headquarter' => 'Hoofdkantoor',
                            'disribution center' => 'Distributie Centra',
                            'location' => 'Filialen',
                        ]
                    ),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Land')
                    ->translateLabel()
                    ->options(
                        [
                            'NL' => CountriesEnum::NL->getLabel(),
                            'BE' => CountriesEnum::BE->getLabel(),
                        ]
                    ),

                Tables\Filters\TrashedFilter::make()
                    ->label(__('Verwijderde adressen'))
                    ->visible(true),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Nieuw Adres'))
                    ->icon('heroicon-o-plus'),
                Tables\Actions\Action::make('erp_account_users')
                    ->label(__('Synchroniseren met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountAddresses($this->getOwnerRecord());
                    })
                    ->visible(function() {
                        return ($this->getOwnerRecord()->erp_status->value === '200');
                    }),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('environment', $this->getOwnerRecord()->environment)
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }


}
