<?php

namespace App\Filament\Resources\Copies\Pages;

use App\Filament\Resources\Copies\CopyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCopies extends ListRecords
{
    protected static string $resource = CopyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
