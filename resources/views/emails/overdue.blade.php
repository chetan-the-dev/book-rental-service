@component('mail::message')
# Overdue Rental Notice

Dear {{ $rental->user_name }},

This is a reminder that the book "{{ $rental->book_title }}" you rented is overdue.

Please return it as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent