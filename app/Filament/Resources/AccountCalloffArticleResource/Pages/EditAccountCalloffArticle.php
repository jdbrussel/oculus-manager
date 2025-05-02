<?php

namespace App\Filament\Resources\AccountCalloffArticleResource\Pages;

use App\Filament\Resources\AccountCalloffArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountCalloffArticle extends EditRecord
{
    protected static string $resource = AccountCalloffArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
