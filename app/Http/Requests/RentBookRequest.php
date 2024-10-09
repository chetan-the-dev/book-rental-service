<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name'=>'required|string',
            'user_email'=>'required|email',
        ];
    }
}
