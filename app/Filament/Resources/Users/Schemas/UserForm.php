<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Copy;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'librarian' => 'Librarian',
                        'patron' => 'Patron',
                    ])
                    ->default('patron')
                    ->required()
                    ->searchable(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('rfid_uid')
                    ->label('RFID UID')
                    ->helperText('Must be globally unique and not match any book copy RFID.')
                    ->maxLength(32)
                    ->rules([
                        fn ($record) => Rule::unique('users', 'rfid_uid')->ignore($record?->id),
                        function (string $attribute, $value, \Closure $fail) {
                            if (blank($value)) {
                                return;
                            }

                            $normalized = strtoupper(trim((string) $value));
                            if (Copy::whereRaw('UPPER(barcode) = ?', [$normalized])->exists()) {
                                $fail('This RFID UID is already assigned to a book copy.');
                            }
                        },
                    ])
                    ->dehydrateStateUsing(fn ($state) => blank($state) ? null : strtoupper(trim($state))),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Users\Pages\CreateUser)
                    ->minLength(8)
                    ->label('Password (leave blank to keep current)'),
                Textarea::make('two_factor_secret')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('two_factor_recovery_codes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('two_factor_confirmed_at'),
            ]);
    }
}
