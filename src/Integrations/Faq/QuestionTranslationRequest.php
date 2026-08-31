<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Illuminate\Foundation\Http\FormRequest;

class QuestionTranslationRequest extends FormRequest
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
            'answer' => ['required', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(FaqIntegration::QUESTIONS) === true;
    }
}
