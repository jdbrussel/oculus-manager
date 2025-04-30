<?php

namespace App\Filament\Resources\AccountPackageResource\Pages;

use App\Filament\Resources\AccountPackageResource;
use App\Filament\Resources\AccountPackageResource\Widgets\AccountPackageWidget;
use Filament\Resources\Pages\EditRecord;

class EditAccountPackage extends EditRecord
{
    protected static string $resource = AccountPackageResource::class;

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
