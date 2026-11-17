<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class OpenApplicationRequest extends FormRequest
{
     /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
         return [
            'role' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email','max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'social_media_url' => ['nullable', 'string', 'max:255','url'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx','max:2048'],
            'message' => ['required', 'string','max:255']
        ];
    }
}
