<?php

namespace App\Filament\Resources\Holds;

use App\Filament\Resources\Holds\Pages\CreateHold;
use App\Filament\Resources\Holds\Pages\EditHold;
use App\Filament\Resources\Holds\Pages\ListHolds;
use App\Filament\Resources\Holds\Pages\ViewHold;
use App\Filament\Resources\Holds\Schemas\HoldForm;
use App\Filament\Resources\Holds\Schemas\HoldInfolist;
use App\Filament\Resources\Holds\Tables\HoldsTable;
use App\Models\Hold;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HoldResource extends Resource
{
    protected static ?string $model = Hold::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string | \UnitEnum | null $navigationGroup = 'Circulation';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return HoldForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HoldInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HoldsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHolds::route('/'),
            'create' => CreateHold::route('/create'),
            'view' => ViewHold::route('/{record}'),
            'edit' => EditHold::route('/{record}/edit'),
        ];
    }
}
