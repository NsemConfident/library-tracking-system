<?php

namespace App\Filament\Resources\Copies\Pages;

use App\Filament\Resources\Copies\CopyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCopy extends ViewRecord
{
    protected static string $resource = CopyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
