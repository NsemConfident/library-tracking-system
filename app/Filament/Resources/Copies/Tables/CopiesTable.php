<?php

namespace App\Filament\Resources\Copies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CopiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('book.author')
                    ->searchable()
                    ->label('Author'),
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Barcode'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'checked_out' => 'warning',
                        'on_hold' => 'info',
                        'missing' => 'danger',
                        'damaged' => 'danger',
                        'maintenance' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('acquired_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
