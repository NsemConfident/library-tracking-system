<?php

namespace App\Filament\Resources\Copies;

use App\Filament\Resources\Copies\Pages\CreateCopy;
use App\Filament\Resources\Copies\Pages\EditCopy;
use App\Filament\Resources\Copies\Pages\ListCopies;
use App\Filament\Resources\Copies\Pages\ViewCopy;
use App\Filament\Resources\Copies\Schemas\CopyForm;
use App\Filament\Resources\Copies\Schemas\CopyInfolist;
use App\Filament\Resources\Copies\Tables\CopiesTable;
use App\Models\Copy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CopyResource extends Resource
{
    protected static ?string $model = Copy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $recordTitleAttribute = 'barcode';

    protected static string | \UnitEnum | null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CopyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CopyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CopiesTable::configure($table);
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
            'index' => ListCopies::route('/'),
            'create' => CreateCopy::route('/create'),
            'view' => ViewCopy::route('/{record}'),
            'edit' => EditCopy::route('/{record}/edit'),
        ];
    }
}
