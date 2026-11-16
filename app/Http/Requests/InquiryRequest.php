<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class InquiryRequest extends FormRequest
{
    
    public function rules(): array
    {
         return [
            'role' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email','max:100'],
            'site_url' => ['nullable', 'string', 'max:255','url'],
            'reason' => ['required', 'string','max:255'],
            'company_name' => ['nullable', 'string','max:255'],
            'message' => ['required', 'string','max:255']
        ];
    }
}
