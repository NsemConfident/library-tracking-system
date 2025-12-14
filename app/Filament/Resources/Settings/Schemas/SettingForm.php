<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Setting Key')
                    ->helperText('Unique identifier for this setting (e.g., loan_period_days)'),
                TextInput::make('value')
                    ->required()
                    ->label('Value')
                    ->helperText('The setting value'),
                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'float' => 'Float',
                        'boolean' => 'Boolean',
                    ])
                    ->default('string')
                    ->required()
                    ->label('Type'),
                Select::make('group')
                    ->options([
                        'general' => 'General',
                        'loans' => 'Loans',
                        'fines' => 'Fines',
                        'holds' => 'Holds',
                        'notifications' => 'Notifications',
                    ])
                    ->default('general')
                    ->required()
                    ->label('Group'),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
