<?php declare(strict_types=1);

namespace App\Http\Requests\Wallets;

use Illuminate\Foundation\Http\FormRequest;
use Override;

final class GetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid',
        ];
    }

    public function getId(): string
    {
        return $this->route('id');
    }
}
