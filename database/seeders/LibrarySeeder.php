<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Copy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@library.com'],
            [
                'name' => 'Library Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create librarian user
        $librarian = User::firstOrCreate(
            ['email' => 'librarian@library.com'],
            [
                'name' => 'Jane Librarian',
                'password' => bcrypt('password'),
                'role' => 'librarian',
                'email_verified_at' => now(),
            ]
        );

        // Create patron users
        $patrons = User::factory(5)->create([
            'role' => 'patron',
            'email_verified_at' => now(),
        ]);

        // Sample books
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'description' => 'A classic American novel about the Jazz Age.',
                'publisher' => 'Scribner',
                'published_year' => 1925,
                'category' => 'Fiction',
                'pages' => 180,
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'description' => 'A gripping tale of racial injustice and childhood innocence.',
                'publisher' => 'J.B. Lippincott & Co.',
                'published_year' => 1960,
                'category' => 'Fiction',
                'pages' => 376,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780452284234',
                'description' => 'A dystopian social science fiction novel.',
                'publisher' => 'Secker & Warburg',
                'published_year' => 1949,
                'category' => 'Science Fiction',
                'pages' => 328,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => '9780141439518',
                'description' => 'A romantic novel of manners.',
                'publisher' => 'T. Egerton',
                'published_year' => 1813,
                'category' => 'Romance',
                'pages' => 432,
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'isbn' => '9780316769174',
                'description' => 'A controversial novel about teenage rebellion.',
                'publisher' => 'Little, Brown and Company',
                'published_year' => 1951,
                'category' => 'Fiction',
                'pages' => 234,
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780544003415',
                'description' => 'An epic high fantasy novel.',
                'publisher' => 'Allen & Unwin',
                'published_year' => 1954,
                'category' => 'Fantasy',
                'pages' => 1178,
            ],
            [
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'isbn' => '9780747532699',
                'description' => 'The first book in the Harry Potter series.',
                'publisher' => 'Bloomsbury',
                'published_year' => 1997,
                'category' => 'Fantasy',
                'pages' => 223,
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780547928227',
                'description' => 'A fantasy novel about Bilbo Baggins.',
                'publisher' => 'Allen & Unwin',
                'published_year' => 1937,
                'category' => 'Fantasy',
                'pages' => 310,
            ],
        ];

        foreach ($books as $bookData) {
            $book = Book::create($bookData);

            // Create 2-3 copies of each book
            $numCopies = rand(2, 3);
            for ($i = 1; $i <= $numCopies; $i++) {
                Copy::create([
                    'book_id' => $book->id,
                    'barcode' => 'BK' . str_pad($book->id, 6, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'status' => 'available',
                    'location' => 'Shelf ' . chr(65 + ($book->id % 10)) . '-' . rand(1, 20),
                    'acquired_date' => now()->subDays(rand(30, 365)),
                ]);
            }
        }

        $this->command->info('Library seeded successfully!');
        $this->command->info('Admin: admin@library.com / password');
        $this->command->info('Librarian: librarian@library.com / password');
    }
}
