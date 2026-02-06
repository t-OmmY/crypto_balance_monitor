<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallets;

use Illuminate\Foundation\Http\FormRequest;

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
        $supportedCurrencies = (array) config('app.supported_currencies');

        return [
            'address' => ['required', 'string'],
            'currency' => ['required', 'in:' . implode(',', $supportedCurrencies)],
        ];
    }

    public function getAddress(): string
    {
        return $this->get('address');
    }

    public function getCurrency(): string
    {
        return $this->get('currency');
    }
}
