<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    private const int DEFAULT_PER_PAGE = 5;

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
            'per_page' => 'integer|min:1',
            'page' => 'integer|min:1'
        ];
    }

    public function getPerPage(): int
    {
        return $this->get('per_page', self::DEFAULT_PER_PAGE);
    }
}
