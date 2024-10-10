<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rental;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    protected $model = Rental::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name' => $this->faker->name,
            'user_email' => $this->faker->safeEmail,
            'book_id' => \App\Models\Book::factory(), // Assuming you have a Book factory
            'rented_at' => now(),
            'due_at' => now()->addWeeks(2),
            'returned_at' => null,
            'is_overdue' => false,
        ];
    }
}
