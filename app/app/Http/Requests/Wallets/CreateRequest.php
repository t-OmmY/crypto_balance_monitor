<?php declare(strict_types=1);

namespace app\Http\Requests\Wallets;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'address' => ['required', 'string'],
            'currency' => ['required', 'in:BTC,LTC,ETH'], //todo move to configuration
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
