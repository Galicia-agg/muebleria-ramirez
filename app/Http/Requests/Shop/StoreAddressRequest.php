<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'department' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:100'],
            'zone' => ['nullable', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ];
    }
}
