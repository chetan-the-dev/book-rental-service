<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('books/search', [BookController::class, 'searchBooks']);
Route::post('books/{book}/rent', [RentalController::class, 'rentBook']);
Route::post('rentals/{rental}/return', [RentalController::class, 'returnBook']);
Route::get('rentals/history', [RentalController::class, 'rentalHistory']);
Route::get('stats', [RentalController::class, 'stats']);
