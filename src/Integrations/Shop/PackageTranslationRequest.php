<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Illuminate\Foundation\Http\FormRequest;

class PackageTranslationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
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
