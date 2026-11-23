<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Ödeme formu doğrulaması: adres seçimi + kart bilgileri.
 * Kart numarası Luhn algoritmasıyla, son kullanma tarihi geleceğe göre kontrol edilir.
 * (Gerçek bir ödeme yapılmaz; sadece sunum/test amaçlıdır.)
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'card_number' => preg_replace('/\D/', '', (string) $this->card_number),
        ]);
    }

    public function rules(): array
    {
        return [
            'address_id'   => ['required', 'integer'],
            'card_holder'  => ['required', 'string', 'min:5', 'max:60', 'regex:/^[\pL\s]+$/u'],
            'card_number'  => ['required', 'digits:16'],
            'card_month'   => ['required', 'integer', 'between:1,12'],
            'card_year'    => ['required', 'integer', 'between:' . now()->year . ',' . (now()->year + 15)],
            'card_cvv'     => ['required', 'digits:3'],
            'installment'  => ['required', 'in:' . implode(',', config('shop.installments'))],
            'note'         => ['nullable', 'string', 'max:500'],
            'agreement'    => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'address_id'  => 'teslimat adresi',
            'card_holder' => 'kart üzerindeki isim',
            'card_number' => 'kart numarası',
            'card_month'  => 'son kullanma ayı',
            'card_year'   => 'son kullanma yılı',
            'card_cvv'    => 'güvenlik kodu (CVV)',
            'installment' => 'taksit seçeneği',
            'note'        => 'sipariş notu',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Lütfen bir teslimat adresi seçin.',
            'card_holder.regex'   => 'Kart üzerindeki isim sadece harflerden oluşmalıdır.',
            'agreement.accepted'  => 'Demo satış koşullarını okuduğunuzu onaylayın.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if (! $this->user()->addresses()->whereKey($this->address_id)->exists()) {
                    $validator->errors()->add('address_id', 'Seçilen adres bulunamadı.');
                }

                if (! self::luhn($this->card_number)) {
                    $validator->errors()->add('card_number', 'Geçersiz kart numarası.');
                }

                $expiry = now()->setDate((int) $this->card_year, (int) $this->card_month, 1)->endOfMonth();
                if ($expiry->isPast()) {
                    $validator->errors()->add('card_month', 'Kartınızın son kullanma tarihi geçmiş.');
                }
            },
        ];
    }

    public static function luhn(string $number): bool
    {
        $sum = 0;
        $alt = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = (int) $number[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = ! $alt;
        }

        return $number !== '' && $sum % 10 === 0;
    }
}
