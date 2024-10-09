<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentBookRequest;
use App\Models\Book;
use App\Models\Rental;
use Illuminate\Http\Request;

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
}
