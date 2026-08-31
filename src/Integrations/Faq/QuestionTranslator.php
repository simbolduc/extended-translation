<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\FAQ\Models\Question;
use Illuminate\Support\Collection;
use WeakMap;

class QuestionTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, QuestionTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Question, array{name: string, answer: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the question's visible fields with the translation for the current locale.
     */
    public function apply(Question $question): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$question])) {
            $attributes = $question->getAttributes();

            $this->originals[$question] = [
                'name' => $attributes['name'] ?? '',
                'answer' => $attributes['answer'] ?? '',
            ];
        }

        $original = $this->originals[$question];
        $translation = $this->forQuestion($question, $this->locales->current());

        foreach (['name', 'answer'] as $field) {
            $value = $translation?->{$field};

            $question->setAttribute(
                $field,
                is_string($value) && $value !== '' ? $value : $original[$field]
            );
            $question->syncOriginalAttribute($field);
        }
    }

    private function forQuestion(Question $question, ?string $locale = null): ?QuestionTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByQuestion()->get($question->id);

        if ($byLocale === null) {
            return null;
        }

        foreach ($candidates as $candidate) {
            $translation = $byLocale->get($candidate);

            if ($translation !== null) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Collection<string, QuestionTranslation>>
     */
    public function allByQuestion(): Collection
    {
        return $this->translations ??= QuestionTranslation::query()
            ->get()
            ->groupBy('question_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
