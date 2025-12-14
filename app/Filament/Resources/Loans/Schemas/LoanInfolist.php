<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('copy.id')
                    ->label('Copy'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('checkout_date')
                    ->date(),
                TextEntry::make('due_date')
                    ->date(),
                TextEntry::make('returned_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('checked_out_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('returned_by')
                    ->numeric()
                    ->placeholder('-'),
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
