<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchBooksRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{   
    /**
    * Search for books based on optional filters: name, genre.
    *
    * Request Type: POST
    *
    * @param SearchBooksRequest $request
    * @return JsonResponse
    */
    public function searchBooks(SearchBooksRequest $request): JsonResponse {

        // Set the default per-page limit or use the one from the request
        $perPage = $request->input('per_page', 10); // Default is 10 items per page

        $books = Book::query()
            ->when($request->input('name'), function ($query, $request) {
                return $query->filterByName($request->input('name'));
            })
            ->when($request->input('genre'), function ($query, $request) {
                return $query->filterByGenre($request->input('genre'));
            })            
            ->where('available', true)
            ->paginate($perPage);

        // If no books found, return 404 with a message
        if ($books->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No books found',
            ], 404);
        }

        // Return the found books with a success message
        return response()->json([
            'status' => 'success',
            'message' => 'Books retrieved successfully',
            'data' => BookResource::collection($books),// $books->items(),
            'pagination' => $this->formatPaginationData($books)
        ], 200);
    }

    /**
    * Format the pagination data for the response.
    * 
    * @param \Illuminate\Pagination\LengthAwarePaginator $books
    * @return array
    */
    protected function formatPaginationData($books)
    {
        return [
            'total' => $books->total(),
            'count' => $books->count(),
            'per_page' => $books->perPage(),
            'current_page' => $books->currentPage(),
            'total_pages' => $books->lastPage(),
        ];
    }
}
