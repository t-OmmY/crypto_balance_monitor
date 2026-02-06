<?php

declare(strict_types=1);

namespace App\Http\Requests\Wallets;

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
            'address' => ['required', new WalletAddressRule($this->getCurrency())],
            'currency' => ['required', Rule::in((array) config('app.supported_currencies'))],
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
