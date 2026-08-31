<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Azuriom\Plugin\FAQ\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminQuestionsComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private QuestionTranslator $translator,
    ) {
        //
    }

    public function compose(View $view): void
    {
        if (! PluginOptions::injectCoreAdmin()) {
            return;
        }

        $data = $view->getData();
        $payload = [
            'label' => trans('extended-translation::admin.actions.translate'),
            'indexUrl' => route('extended-translation.admin.faq.index'),
        ];

        if (isset($data['question']) && $data['question'] instanceof Question) {
            $payload['questionId'] = $data['question']->id;
            $payload['editUrl'] = $this->editUrl($data['question']);
        }

        if (isset($data['questions'])) {
            $payload['questions'] = Collection::make($data['questions'])
                ->filter(fn ($question) => $question instanceof Question)
                ->mapWithKeys(fn (Question $question) => [$question->id => $this->editUrl($question)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.faq.inject', ['payload' => $payload])->render()
        );
    }

    protected function editUrl(Question $question): string
    {
        $existing = $this->translator->allByQuestion()->get($question->id)?->keys() ?? collect();

        return route('extended-translation.admin.faq.edit', [
            $question,
            $this->locales->firstTargetLocale($existing),
        ]);
    }
}
