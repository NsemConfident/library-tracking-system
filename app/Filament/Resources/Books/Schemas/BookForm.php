<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                TextInput::make('isbn')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('ISBN'),
                Select::make('category')
                    ->options([
                        'Fiction' => 'Fiction',
                        'Non-Fiction' => 'Non-Fiction',
                        'Science Fiction' => 'Science Fiction',
                        'Fantasy' => 'Fantasy',
                        'Romance' => 'Romance',
                        'Mystery' => 'Mystery',
                        'Biography' => 'Biography',
                        'History' => 'History',
                        'Science' => 'Science',
                        'Technology' => 'Technology',
                    ])
                    ->searchable(),
                TextInput::make('publisher')
                    ->maxLength(255),
                TextInput::make('published_year')
                    ->numeric()
                    ->minValue(1000)
                    ->maxValue(now()->year),
                TextInput::make('language')
                    ->default('en')
                    ->maxLength(10),
                TextInput::make('pages')
                    ->numeric()
                    ->minValue(1),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->image()
                    ->directory('book-covers')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
