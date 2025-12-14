<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('copy_id')
                    ->relationship('copy', 'barcode', fn ($query) => $query->where('status', 'available'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Book Copy (Barcode)'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Patron'),
                DatePicker::make('checkout_date')
                    ->required()
                    ->default(now())
                    ->displayFormat('Y-m-d'),
                DatePicker::make('due_date')
                    ->required()
                    ->default(now()->addDays(14))
                    ->displayFormat('Y-m-d'),
                DatePicker::make('returned_date')
                    ->displayFormat('Y-m-d'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'returned' => 'Returned',
                        'overdue' => 'Overdue',
                        'lost' => 'Lost',
                    ])
                    ->default('active')
                    ->required()
                    ->disabled(fn ($record) => $record && $record->status === 'returned'),
                Select::make('checked_out_by')
                    ->relationship('checkedOutBy', 'name')
                    ->default(fn () => auth()->id())
                    ->disabled(),
                Select::make('returned_by')
                    ->relationship('returnedBy', 'name')
                    ->disabled(),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
