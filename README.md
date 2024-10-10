# Book Rental Service API

This is a RESTful API for a book rental service built with Laravel 11. It allows users to search for books, rent them, return them, and view their rental history. The API also automatically marks rentals as overdue if they are not returned within 2 weeks and sends email notifications for overdue rentals.

## Features

- **Search for Books**: Users can search for books by name and/or genre.
- **Rent a Book**: Users can rent a book for a maximum period of 2 weeks.
- **Return a Book**: Users can return rented books.
- **View Rental History**: Users can view their past rental history.
- **Overdue Rentals**: Automatically marks rentals as overdue after 2 weeks.
- **Email Notifications**: Sends notifications to users when their rentals become overdue.
- **Statistics**: Provides statistics on the most overdue book, the most popular, and the least popular books.

## Requirements

- PHP 8.1 or higher
- Laravel 11
- Composer
- MySQL or any other database of your choice
- Mail server for sending notifications

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/chetan-the-dev/book-rental-service.git
   cd book-rental-service

2. **Install dependencies:**
   composer install

3. **Set up your environment:**
   Copy the .env.example file to .env:
   cp .env.example .env
   Make sure in .env file MAIL_MAILER value set to log or use proper mail account details to test mails functionality
   Update the .env file with your database and mail configuration.

4. **Generate application key:**
   php artisan key:generate

5. **Run migrations:**
   php artisan migrate

6. **Seed the database:**
   php artisan db:seed --class=BooksTableSeeder

7. **Setup PHP unit:**
   Copy .env file to .env.test file this fill will store test data in database. 
   cp .env .env.testing    
   Update the .env.test file with your test database. You need to create seprate database for this and add database name in .env.test file.
   php artisan migrate --env=testing

   You can execute below comand to test the test cases
   php artisan test

## Update rent to overdue
   - For testing you can execute below command to update rent as over dur which due date passed and send email.
        php artisan rental:update-overdue
   - I have Stored email in laravel log file which is located at rootDirectory/storage/logs/laravel.log file.
   

## API Collection
   Below is the link for api collections for book rental services.
   Link: https://drive.google.com/file/d/1byOgI_JGoZZCsrTeplQT4k1wmlKQVdg3/view?usp=sharing
   Update api end point as your setup.


