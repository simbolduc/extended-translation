<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Illuminate\Foundation\Http\FormRequest;

class PageTranslationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(WikiIntegration::WIKI) === true;
    }
}
