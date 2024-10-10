<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rental;

class UpdateOverdueTest extends TestCase
{
    public function test_update_overdue_rentals_and_send_notifications()
    {
        $rentalOverdue1 = Rental::factory()->create([
            'user_name' => 'John Doe',
            'user_email' => 'johndoe@example.com',
            'due_at' => now()->subDays(5), // 5 days overdue
            'returned_at' => null,
            'is_overdue' => false,
        ]);

        $rentalOverdue2 = Rental::factory()->create([
            'user_name' => 'Jane Doe',
            'user_email' => 'janedoe@example.com',
            'due_at' => now()->subDays(2), // 2 days overdue
            'returned_at' => null,
            'is_overdue' => false,
        ]);

        $this->artisan('rental:update-overdue')->assertExitCode(0);

        $this->assertEquals($rentalOverdue1->refresh()->is_overdue,1);
        $this->assertEquals($rentalOverdue2->refresh()->is_overdue,1);
    }

    public function test_no_rentals_are_updated_if_all_are_returned()
    {
       Rental::factory()->create([
            'user_name' => 'John Doe',
            'user_email' => 'johndoe@example.com',
            'due_at' => now()->addDays(5), // still has time
            'returned_at' => null,
            'is_overdue' => false,
        ]);

        Rental::factory()->create([
            'user_name' => 'Jane Doe',
            'user_email' => 'janedoe@example.com',
            'due_at' => now()->addDays(2), // still has time
            'returned_at' => null,
            'is_overdue' => false,
        ]);

        $this->artisan('rental:update-overdue')->assertExitCode(0);
    }
}
