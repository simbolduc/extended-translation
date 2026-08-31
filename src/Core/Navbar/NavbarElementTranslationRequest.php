<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Navbar;

use Azuriom\Plugin\ExtendedTranslation\Core\Support\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class NavbarElementTranslationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(Permissions::NAVBAR) === true;
    }
}
