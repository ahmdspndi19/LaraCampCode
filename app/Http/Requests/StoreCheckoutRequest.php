<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCheckoutRequest extends FormRequest
{
    // Data kartu tidak boleh ikut ter-flash ke session saat validasi gagal.
    protected $dontFlash = ['password', 'password_confirmation', 'card_number', 'cvc'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Form menampilkan nomor kartu per 4 digit ("4242 4242 ..."),
        // jadi spasi/tanda hubung dibuang dulu sebelum divalidasi.
        $this->merge([
            'card_number' => preg_replace('/\D+/', '', (string) $this->input('card_number')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'digits_between:13,19'],
            'expired' => ['required', 'regex:/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/'],
            'cvc' => ['required', 'digits_between:3,4'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->has('expired')) {
                    return;
                }

                if ($this->expiryDate()->isPast()) {
                    $validator->errors()->add('expired', 'Kartu sudah kedaluwarsa.');
                }
            },
        ];
    }

    /** Akhir bulan dari masa berlaku kartu (format MM/YY atau MM/YYYY). */
    public function expiryDate(): Carbon
    {
        $value = (string) $this->input('expired');

        return Carbon::createFromFormat(
            strlen($value) === 5 ? '!m/y' : '!m/Y',
            $value
        )->endOfMonth();
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'occupation' => 'pekerjaan',
            'card_number' => 'nomor kartu',
            'expired' => 'masa berlaku',
            'cvc' => 'CVC',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'max' => ':Attribute maksimal :max karakter.',
            'card_number.digits_between' => 'Nomor kartu harus 13-19 digit.',
            'expired.regex' => 'Format masa berlaku harus MM/YY atau MM/YYYY.',
            'cvc.digits_between' => 'CVC harus 3-4 digit.',
        ];
    }
}
