<?php

namespace App\Filament\Resources\Loans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('copy.barcode')
                    ->searchable()
                    ->label('Barcode')
                    ->sortable(),
                TextColumn::make('copy.book.title')
                    ->searchable()
                    ->label('Book')
                    ->weight('bold')
                    ->limit(30),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Patron'),
                TextColumn::make('checkout_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record && $record->isOverdue() ? 'danger' : null),
                TextColumn::make('returned_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'returned' => 'gray',
                        'overdue' => 'danger',
                        'lost' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('days_overdue')
                    ->label('Days Overdue')
                    ->getStateUsing(fn ($record) => $record?->days_overdue ?? 0)
                    ->badge()
                    ->color('danger')
                    ->visible(fn ($record) => $record && $record->isOverdue()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('checkout_date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
