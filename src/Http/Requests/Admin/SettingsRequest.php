<?php

namespace Azuriom\Plugin\ExtendedTranslation\Http\Requests\Admin;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\Permissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The attributes represented by checkboxes.
     *
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'inject_core_admin',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $codes = app(LocaleCatalog::class)->installed()->keys()->all();

        return [
            'locales' => ['required', 'array', 'min:1'],
            'locales.*' => ['required', 'string', Rule::in($codes)],
            'inject_core_admin' => ['required', 'boolean'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(Permissions::SETTINGS) === true;
    }
}
