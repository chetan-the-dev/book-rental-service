<?php

use App\Mail\OverdueRentalMail;
use App\Models\Rental;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\OverdueNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;


app(Schedule::class)->command('rental:update-overdue')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('rental:update-overdue', function () {
    // Mark rentals as overdue
    Rental::whereNull('returned_at')
        ->where('due_at', '<', now())
        ->update(['is_overdue' => true]);

    // Send email notifications for overdue rentals
    Rental::where('is_overdue', true)->whereNull('returned_at')->chunk(50, function($rentals) {
        foreach ($rentals as $rental) {
            // Ensure user email exists
            if ($rental->user_email) {
                Mail::to($rental->user_email)->send(new OverdueRentalMail($rental));
            }
        }
    });
})->describe('Update overdue rentals and send notifications.');