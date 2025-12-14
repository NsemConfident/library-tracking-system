<?php

namespace App\Filament\Resources\Holds\Pages;

use App\Filament\Resources\Holds\HoldResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHold extends ViewRecord
{
    protected static string $resource = HoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
