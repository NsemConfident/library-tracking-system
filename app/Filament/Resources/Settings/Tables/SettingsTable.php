<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->label('Key')
                    ->copyable(),
                TextColumn::make('value')
                    ->searchable()
                    ->label('Value')
                    ->copyable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'integer' => 'info',
                        'float' => 'warning',
                        'boolean' => 'success',
                        default => 'gray',
                    })
                    ->label('Type'),
                TextColumn::make('group')
                    ->badge()
                    ->color('primary')
                    ->label('Group'),
                TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->label('Description'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'loans' => 'Loans',
                        'fines' => 'Fines',
                        'holds' => 'Holds',
                        'notifications' => 'Notifications',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'float' => 'Float',
                        'boolean' => 'Boolean',
                    ]),
            ])
            ->defaultSort('group')
            ->groups([
                'group',
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
