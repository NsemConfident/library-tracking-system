<?php

namespace App\Filament\Resources\Holds\Pages;

use App\Filament\Resources\Holds\HoldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHolds extends ListRecords
{
    protected static string $resource = HoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
