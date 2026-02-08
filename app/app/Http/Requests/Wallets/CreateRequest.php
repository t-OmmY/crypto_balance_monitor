<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallets;

use App\Enums\Currency;
use App\Rules\WalletAddressRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                Rule::in(Currency::values())
            ],
            'address' => [
                'required',
                'string',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('currency')) {
                return;
            }

            $rule = new WalletAddressRule($this->getCurrency());

            $rule->validate('address', $this->getAddress(), function ($message) use ($validator) {
                $validator->errors()->add('address', $message);
            });
        });
    }

    public function getAddress(): string
    {
        return $this->get('address');
    }

    public function getCurrency(): Currency
    {
        return Currency::from($this->input('currency'));
    }
}
