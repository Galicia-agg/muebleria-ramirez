<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'delivery_method' => ['required', 'in:domicilio,recoger_tienda'],
            'address_id' => ['required_if:delivery_method,domicilio', 'nullable', 'exists:addresses,id'],
            'payment_method' => ['required', 'in:transferencia,contra_entrega,tarjeta_online'],
            'payment_proof' => ['required_if:payment_method,transferencia', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:6144'],
        ];
    }
}
