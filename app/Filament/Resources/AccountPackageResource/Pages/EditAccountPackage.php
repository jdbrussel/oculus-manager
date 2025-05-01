<?php

namespace App\Filament\Resources\AccountPackageResource\Pages;

use App\Filament\Resources\AccountPackageResource;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageWidget;
use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditAccountPackage extends EditRecord
{
    protected static string $resource = AccountPackageResource::class;

    public function getTitle(): string|Htmlable
    {
        return "{$this->record->account->name} ({$this->record->account->erp_id})";
    }

    public function getBreadcrumbs(): array
    {
//        /admin/accounts/1/edit?activeRelationManager=4
       // dd($this->record->account->name);
        $breadcrumbs = [
            AccountResource::getUrl('index') => __('Accounts'),
//            AccountResource::getUrl('edit',[ 'record' => $this->record->account ] ) => $this->record->account->name,
            AccountResource::getUrl('edit',[ 'record' => $this->record->account ] ) . "?activeRelationManager=4" => $this->record->account->name,
            0 =>  __('Pakket') . " " . $this->record->erp_id
        ];
        return  $breadcrumbs;
    }
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Pakket: '. $this->record->erp_id;
    }
    public function getContentTabIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
//            Actions\RestoreAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccountPackageResource\Widgets\AccountPackageWidget::class,
        ];
    }


}
