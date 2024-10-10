<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

class RentControllerTest extends TestCase
{
    /**
     * Test renting a book successfully.
     */
    public function test_rent_book_successfully()
    {
        // Create a book that is available for rent
        $book = Book::factory()->create(['available' => true]);

        // Prepare request payload
        $payload = [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com'
        ];

        // Send POST request to rent the book
        $response = $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Assert that the response is successful
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Book rented successfully'
                 ]);

        // Assert that the book is marked as unavailable
        $book->refresh();
        $this->assertEquals($book->available,0);

        // Assert that the rental record is created in the database
        $this->assertDatabaseHas('rentals', [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
            'book_id' => $book->id,
        ]);
    }

    /**
     * Test trying to rent a book that is not available.
     */
    public function test_rent_book_not_available()
    {
        // Create a book that is not available for rent
        $book = Book::factory()->create(['available' => false]);

        // Prepare request payload
        $payload = [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com'
        ];

        // Send POST request to rent the book
        $response = $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Assert that the response indicates the book is unavailable
        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Book is not available'
                 ]);

        // Assert that no rental record was created
        $this->assertDatabaseMissing('rentals', [
            'book_id' => $book->id,
        ]);
    }

    /**
     * Test renting a book with missing fields.
     */
    public function test_rent_book_with_missing_fields()
    {
        // Create a book that is available for rent
        $book = Book::factory()->create(['available' => true]);

        // Prepare incomplete request payload
        $payload = [
            'user_name' => 'John Doe'
            // Missing user_email
        ];

        // Send POST request to rent the book
        $response = $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Assert that the response status indicates validation error (422)
        $response->assertStatus(422);
    }

    /**
     * Test renting a book when two users attempt to rent it simultaneously.
     */
    public function test_race_condition_renting_book()
    {
        // Create a book that is available for rent
        $book = Book::factory()->create(['available' => true]);

        // Prepare two payloads for two users trying to rent the same book
        $payload1 = [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com'
        ];

        $payload2 = [
            'user_name' => 'Jane Smith',
            'user_email' => 'jane@example.com'
        ];

        // Use DB transaction to simulate simultaneous requests
        DB::beginTransaction();

        // Send the first request
        $response1 = $this->postJson('/api/books/' . $book->id . '/rent', $payload1);

        // Mark the book as unavailable before second request commits
        $book->update(['available' => false]);

        // Send the second request, simulating a race condition
        $response2 = $this->postJson('/api/books/' . $book->id . '/rent', $payload2);

        // Commit transaction
        DB::commit();

        // Assert the first response is successful
        $response1->assertStatus(200)
                  ->assertJson([
                      'status' => 'success',
                      'message' => 'Book rented successfully'
                  ]);

        // Assert the second response returns an error since the book is now unavailable
        $response2->assertStatus(400)
                  ->assertJson([
                      'status' => 'error',
                      'message' => 'Book is not available'
                  ]);
    }

    /**
     * Test renting a book with an invalid email format.
     */
    public function test_rent_book_with_invalid_email()
    {
        // Create a book that is available for rent
        $book = Book::factory()->create(['available' => true]);

        // Prepare request payload with invalid email
        $payload = [
            'user_name' => 'John Doe',
            'user_email' => 'invalid-email'  // Invalid email format
        ];

        // Send POST request to rent the book
        $response = $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Assert that the response status indicates validation error (422)
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('user_email');
    }

    /**
     * Test renting the same book twice within the 2-week period.
     */
    public function test_rent_book_twice_within_two_weeks()
    {
        // Create a book that is available for rent
        $book = Book::factory()->create(['available' => true]);

        // Prepare request payload for the first rental
        $payload = [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com'
        ];

        // Send POST request to rent the book
        $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Mark the book as returned manually (to simulate early return)
        $book->update(['available' => true]);

        // Attempt to rent the same book again within the 2-week rental period
        $response = $this->postJson('/api/books/' . $book->id . '/rent', $payload);

        // Assert that the system prevents the rental because the user has already rented it
        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Book is not available'
                ]);
    }

    /**
     * Test returning a book successfully.
     */
    public function test_return_book_successfully()
    {
        // Create a book and a rental record
        $book = Book::factory()->create(['available' => false]); // Book is not available for rent
        $rental = Rental::factory()->create(['book_id' => $book->id, 'returned_at' => null]);

        // Send POST request to return the book
        $response = $this->postJson('/api/rentals/' . $rental->id.'/return');

        // Assert that the response is successful
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Book returned successfully'
                 ]);

        // Assert that the rental record is updated with the returned timestamp
        $rental->refresh();
        $this->assertNotNull($rental->returned_at);

        // Assert that the book is now available
        $book->refresh();
        $this->assertEquals($book->available,1);
    }

    /**
     * Test attempting to return a book that has already been returned.
     */
    public function test_return_book_already_returned()
    {
        // Create a book and a rental record
        $book = Book::factory()->create(['available' => false]); // Book is not available for rent
        $rental = Rental::factory()->create(['book_id' => $book->id, 'returned_at' => now()]); // Already returned

        // Send POST request to return the book
        $response = $this->postJson('/api/rentals/' . $rental->id.'/return');

        // Assert that the response indicates the book was already returned
        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Book was already returned'
                 ]);
    }

    /**
     * Test if the book's availability is updated correctly after return.
     */
    public function test_book_availability_updated_after_return()
    {
        // Create a book and a rental record
        $book = Book::factory()->create(['available' => false]); // Book is not available for rent
        $rental = Rental::factory()->create(['book_id' => $book->id, 'returned_at' => null]);

        // Send POST request to return the book
        $this->postJson('/api/rentals/' . $rental->id.'/return');

        // Assert that the book is now available
        $book->refresh();
        $this->assertEquals($book->available,1);
    }
}
