<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'suhu' => ['nullable', 'numeric'],
            'kelembapan' => ['nullable', 'numeric'],
            'potensio' => ['nullable', 'integer'],
        ];
    }
}
