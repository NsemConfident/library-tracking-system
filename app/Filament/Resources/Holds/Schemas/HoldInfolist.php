<?php

namespace App\Filament\Resources\Holds\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HoldInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('book.title')
                    ->label('Book'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('requested_date')
                    ->date(),
                TextEntry::make('expiry_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('fulfilled_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('fulfilledByCopy.id')
                    ->label('Fulfilled by copy')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('position')
                    ->numeric(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
