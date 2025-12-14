<?php

namespace App\Filament\Resources\Holds\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HoldForm
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
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Patron'),
                DatePicker::make('requested_date')
                    ->required()
                    ->default(now())
                    ->displayFormat('Y-m-d'),
                DatePicker::make('expiry_date'),
                DatePicker::make('fulfilled_date'),
                Select::make('fulfilled_by_copy_id')
                    ->relationship('fulfilledByCopy', 'id')
                    ->default(null),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'ready' => 'Ready',
            'fulfilled' => 'Fulfilled',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
