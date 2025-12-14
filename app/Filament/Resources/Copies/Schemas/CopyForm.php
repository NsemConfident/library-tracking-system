<?php

namespace App\Filament\Resources\Copies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CopyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Book'),
                TextInput::make('barcode')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Barcode'),
                Select::make('status')
                    ->options([
            'available' => 'Available',
            'checked_out' => 'Checked out',
            'on_hold' => 'On hold',
            'missing' => 'Missing',
            'damaged' => 'Damaged',
            'maintenance' => 'Maintenance',
        ])
                    ->default('available')
                    ->required(),
                TextInput::make('location')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                DatePicker::make('acquired_date'),
            ]);
    }
}
