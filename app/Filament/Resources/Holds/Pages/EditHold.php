<?php

namespace App\Filament\Resources\Holds\Pages;

use App\Filament\Resources\Holds\HoldResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHold extends EditRecord
{
    protected static string $resource = HoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
