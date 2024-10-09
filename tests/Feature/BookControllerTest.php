<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;

class BookControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** 
     * Return book without filters
     */
    public function testSearchBooksWithoutFilter()
    {
        // Create some available books in the database
        Book::factory()->count(5)->create(['available' => true]);

        // Send the API request
        $response = $this->postJson('/api/books/search', []);

        // Assert the status and JSON structure
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
            'pagination' => ['total', 'count', 'per_page', 'current_page', 'total_pages']
        ]);
    }

    /** 
     * Filter book by book title
     */
    public function testSearchBooksWithTitleFilter()
    {
        Book::factory()->create(['title' => 'Harry Potter', 'available' => true]);
        Book::factory()->create(['title' => 'The Hobbit', 'available' => true]);

        $response = $this->postJson('/api/books/search', ['title' => 'Harry']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Harry Potter']);
        $response->assertJsonMissing(['title' => 'The Hobbit']);
    }

    /** 
     * Filter book by book genre
     */
    public function testSearchBooksWithGenreFilter()
    {
        Book::factory()->create(['genre' => 'Fantasy', 'available' => true]);
        Book::factory()->create(['genre' => 'Sci-Fi', 'available' => true]);

        $response = $this->postJson('/api/books/search', ['genre' => 'Fantasy']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['genre' => 'Fantasy']);
        $response->assertJsonMissing(['genre' => 'Sci-Fi']);
    }

    /** 
     * Return error if no book found
     */
    public function testSearchBooksNoResults()
    {
        $response = $this->postJson('/api/books/search', ['title' => 'NonExistentBook']);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No books found',
        ]);
    }
}
