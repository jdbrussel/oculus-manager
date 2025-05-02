<?php

namespace App\Filament\Resources\AccountCalloffArticleResource\Pages;

use App\Filament\Resources\AccountCalloffArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountCalloffArticles extends ListRecords
{
    protected static string $resource = AccountCalloffArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
