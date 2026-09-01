<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Illuminate\Foundation\Http\FormRequest;

class RewardTranslationRequest extends FormRequest
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
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(VoteIntegration::REWARDS) === true;
    }
}
