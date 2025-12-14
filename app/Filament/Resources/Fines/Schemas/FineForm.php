<?php

namespace App\Filament\Resources\Fines\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Patron'),
                Select::make('loan_id')
                    ->relationship('loan', 'id', fn ($query) => $query->where('status', '!=', 'returned'))
                    ->searchable()
                    ->preload()
                    ->label('Loan (Optional)'),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->minValue(0),
                Select::make('type')
                    ->options(['overdue' => 'Overdue', 'lost' => 'Lost', 'damaged' => 'Damaged', 'other' => 'Other'])
                    ->default('overdue')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'waived' => 'Waived', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->required(),
                DatePicker::make('due_date'),
                DatePicker::make('paid_date'),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('created_by')
                    ->relationship('createdBy', 'name')
                    ->default(fn () => auth()->id())
                    ->disabled()
                    ->label('Created By'),
            ]);
    }
}
