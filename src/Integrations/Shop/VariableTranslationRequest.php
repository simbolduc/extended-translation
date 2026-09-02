<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Illuminate\Foundation\Http\FormRequest;

class VariableTranslationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:200'],
            'options' => ['nullable', 'array'],
            'options.*.value' => ['required', 'string', 'max:100'],
            'options.*.name' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(ShopIntegration::SHOP) === true;
    }
}
