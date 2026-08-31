<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Locale;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $codes = app(LocaleCatalog::class)->enabled()->keys()->all();

        return [
            'locale' => ['required', 'string', Rule::in($codes)],
            'redirect' => ['nullable', 'string', 'max:2048'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
