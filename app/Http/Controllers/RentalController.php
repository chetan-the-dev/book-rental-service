<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentBookRequest;
use App\Http\Resources\RentalResource;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
    * Rent a book
    *
    * For practical only i have used user name and email as payload we can use logged user detail as well
    * 
    * Request Type: POST
    *
    * @param RentBookRequest $request
    * @param Book $book The book instance being rented.
    * @return JsonResponse
    */
    public function rentBook(RentBookRequest $request, Book $book) {
        //Check book is available for rent
        if ($book->available) {
            //Rent a book based on detail sent in payload
            $rental = Rental::create([
                'user_name' => $request->input('user_name'),
                'user_email'=>$request->input('user_email'),
                'book_id' => $book->id,
                'rented_at' => now(),
                'due_at' => now()->addWeeks(2),
            ]);
            
            //Update book availablity
            $book->update(['available' => false]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Book rented successfully'
            ], 200);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Book is not available'
        ], 400);
    } 
    
    /**
     * Handles the return of a rented book.
     * 
     * If the book has not been returned yet, it updates the rental record with the current 
     * timestamp and marks the book as available for rent. If the book has already been 
     * returned, it returns an error message indicating that the book was previously returned.
     *
     * @param Rental $rental The rental record associated with the book being returned.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function returnBook(Rental $rental) {
        if (is_null($rental->returned_at)) {
            $rental->update(['returned_at' => now()]);
            $rental->book->update(['available' => true]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Book returned successfully'],200);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Book was already returned'], 400);
    }

    /**
     * Return book rental history.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function rentalHistory() {

        $rentals = Rental::with('book')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Book return history',
            'data'=> RentalResource::collection($rentals)
        ],200);
    }

    /**
     * Return book stats.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the most overdue, popular and least popilor book.
     */
    public function stats() {
        $mostOverdueBook = Rental::where('is_overdue', true)
        ->select('book_id', DB::raw('COUNT(*) as rental_count'))
        ->with('book')
        ->groupBy('book_id')
        ->orderBy('rental_count', 'DESC')
        ->first();
    
        $mostPopularBook = Rental::select('book_id', DB::raw('COUNT(*) as rental_count'))
        ->with('book')
        ->groupBy('book_id')
        ->orderBy('rental_count', 'DESC')
        ->first();
    
        $leastPopularBook = Rental::select('book_id', DB::raw('COUNT(*) as rental_count'))
        ->with('book')
        ->groupBy('book_id')
        ->orderBy('rental_count', 'ASC')
        ->first();
        
        return response()->json([
            'most_overdue' => $mostOverdueBook->book->title ?? 'N/A',
            'most_popular' => $mostPopularBook->book->title ?? 'N/A',
            'least_popular' => $leastPopularBook->book->title ?? 'N/A',
        ]);
    }    
    
}
