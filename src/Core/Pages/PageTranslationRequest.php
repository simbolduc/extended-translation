<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Pages;

use Azuriom\Plugin\ExtendedTranslation\Core\Support\Permissions;
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
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(Permissions::PAGES) === true;
    }
}
