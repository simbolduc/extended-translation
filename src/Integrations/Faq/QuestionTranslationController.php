<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\FAQ\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private QuestionTranslator $translator,
    ) {
        //
    }

    /**
     * Display the FAQ questions that can be translated.
     */
    public function index(): View
    {
        $questions = Question::query()
            ->orderBy('position')
            ->get();

        return view('extended-translation::admin.faq.index', [
            'questions' => $questions,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translations' => $this->translator->allByQuestion(),
        ]);
    }

    /**
     * Show the form for translating a FAQ question.
     */
    public function edit(Question $question, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = QuestionTranslation::query()
            ->where('question_id', $question->id)
            ->where('locale', $locale)
            ->first();

        $existing = QuestionTranslation::query()
            ->where('question_id', $question->id)
            ->pluck('locale');

        return view('extended-translation::admin.faq.edit', [
            'question' => $question,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a FAQ question translation.
     */
    public function update(QuestionTranslationRequest $request, Question $question, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        QuestionTranslation::query()->updateOrCreate(
            [
                'question_id' => $question->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.faq.updated', $question, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.faq.edit', [$question, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a FAQ question translation.
     */
    public function destroy(Question $question, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        QuestionTranslation::query()
            ->where('question_id', $question->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.faq.deleted', $question, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.faq.edit', [$question, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
