<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\component\Connectors\Oculus\OculusSyncher;
use App\Filament\Custom\Resource\Pages\EditByUserRecord;
use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditAccount extends EditByUserRecord
{
    protected static string $resource = AccountResource::class;

    public function getTitle(): string|Htmlable
    {
        return "{$this->record->name} ({$this->record->erp_id})";
    }

    public function getBreadcrumbs(): array
    {
       $breadcrumbs = parent::getBreadcrumbs();
       $breadcrumbs =  array_slice($breadcrumbs, 0, 1);
       $breadcrumbs[] = $this->record->name;
       return  $breadcrumbs;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getContentTabLabel(): ?string
    {
        return $this->record->name;
    }



    public function getContentTabIcon(): ?string
    {
        return 'heroicon-o-briefcase';
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
//            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = OculusSyncher::synchAccountData($data);
        return parent::mutateFormDataBeforeSave($data);

    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('edit', [ 'record' => $this->record ] );
    }
}
