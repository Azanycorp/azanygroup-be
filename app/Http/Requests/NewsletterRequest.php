<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;

#[FailOnUnknownFields]
class NewsletterRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
          return [
            'email' => ['required', 'email', 'unique:news_letters,email'],
        ];
    }
}
