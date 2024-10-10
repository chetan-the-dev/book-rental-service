<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rental_id' => $this->id,
            'user_name' => $this->user_name,
            'rented_at' => $this->rented_at->toDateTimeString(),
            'due_at' => $this->due_at->toDateTimeString(),
            'returned_at' => $this->returned_at ? $this->returned_at->toDateTimeString() : null,
            'book' => [
                'id' => $this->book->id,
                'title' => $this->book->title,
                'author' => $this->book->author,
            ]
        ];
    }
}
