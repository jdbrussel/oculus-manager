<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\component\Connectors\Oculus\OculusSyncher;
use App\Models\AccountPackage;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class AccountPackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'account_packages';

    protected static ?string $icon = 'heroicon-o-cube';

    protected static ?string $title = 'Pakketten';

    protected static ?string $badge = '';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->modules->contains('slug', 'package-manager');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('erp_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('erp_id')
            ->columns([
                Tables\Columns\TextColumn::make('erp_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('edition')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                TextColumn::make('account_package_items_count')
                    ->badge()
                    ->counts('account_package_items')
                    ->label(__('Items')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('erp_account_users')
                    ->label(__('Synchroniseren met Oculus'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function() {
                        OculusSyncher::synchAccountPackages($this->getOwnerRecord(), false);
                    })
                    ->visible(function() {
                        return ($this->getOwnerRecord()->erp_status->value === '200');
                    }),
            ])
            ->actions([

                Tables\Actions\Action::make('erp_synchronize')
                    ->label(__('Synch Items'))
                    ->color('oculus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->requiresConfirmation()
                    ->action(function(AccountPackage $record) {
                        OculusSyncher::synchAccountPackageItems($record);
                    })
                    ->disabled(fn (AccountPackage $record) : bool => !empty($record->status->isSynchable()['error']) ),
                Tables\Actions\Action::make('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (AccountPackage $record): string => route('filament.admin.resources.account-packages.edit', [
                        'tenant' => filament()->getTenant(),
                        'record' => $record
                    ])),
//                Tables\Actions\DeleteAction::make(),
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
            ->modifyQueryUsing(function (Builder $query) {

                $ACCOUNT = $this->getOwnerRecord();
                $MODULE = $ACCOUNT->modules->where('slug', 'package-manager')->first();
                $DAYS_AHEAD = $MODULE->config['packages_days_ahead'] ?? 8;

                $query
                    ->where('environment', $this->getOwnerRecord()->environment)
                    ->where(
                        'scheduled_delivery_datetime' ,
                        '<=' ,
                        Carbon::now()->addDays($DAYS_AHEAD)->timestamp
                    )
                    ->orderBy('scheduled_delivery_datetime', 'DESC')
                    ->withoutGlobalScopes([SoftDeletingScope::class]);

                return $query;
            });
    }

}
