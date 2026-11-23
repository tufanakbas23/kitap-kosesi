<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:50',
            'full_name'    => 'required|string|max:100',
            'phone'        => ['required', 'regex:/^0?5\d{9}$/'],
            'city'         => 'required|string|max:50',
            'district'     => 'required|string|max:50',
            'neighborhood' => 'nullable|string|max:100',
            'address_line' => 'required|string|min:10|max:255',
            'postal_code'  => 'nullable|digits:5',
            'is_default'   => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'        => 'adres başlığı',
            'full_name'    => 'ad soyad',
            'phone'        => 'telefon',
            'city'         => 'il',
            'district'     => 'ilçe',
            'neighborhood' => 'mahalle',
            'address_line' => 'açık adres',
            'postal_code'  => 'posta kodu',
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Telefon numarası 05XX XXX XX XX formatında olmalıdır.'];
    }
}
