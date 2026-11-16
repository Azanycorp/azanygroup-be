<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class ContactUsRequest extends FormRequest
{
    
    public function rules(): array
    {
         return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email','max:100'],
            'subject' => ['required', 'string','max:255'],
            'company_name' => ['nullable', 'string','max:255'],
            'message' => ['required', 'string','max:255']
        ];
    }
}
