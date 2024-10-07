<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'genre' => 'Classics'
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780060935467',
                'genre' => 'Classics'
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780451524935',
                'genre' => 'Dystopian'
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => '9780141199078',
                'genre' => 'Romance'
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'isbn' => '9780316769488',
                'genre' => 'Classics'
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780547928227',
                'genre' => 'Fantasy'
            ],
            [
                'title' => 'Fahrenheit 451',
                'author' => 'Ray Bradbury',
                'isbn' => '9781451673319',
                'genre' => 'Science Fiction'
            ],
            [
                'title' => 'The Book Thief',
                'author' => 'Markus Zusak',
                'isbn' => '9780375842207',
                'genre' => 'Historical Fiction'
            ],
            [
                'title' => 'Moby-Dick',
                'author' => 'Herman Melville',
                'isbn' => '9781503280786',
                'genre' => 'Classics'
            ],
            [
                'title' => 'War and Peace',
                'author' => 'Leo Tolstoy',
                'isbn' => '9781400079988',
                'genre' => 'Historical Fiction'
            ],
        ];

        // Bulk insert or update the books table using upsert (Eloquent)
        Book::upsert($books, ['isbn'], ['title', 'author', 'genre']);

        // Additionally, generate 10 random books using factories
        Book::factory()->count(10)->create();
    }
}
