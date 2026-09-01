<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Illuminate\View\View;

class TitleViewComposer
{
    public function __construct(
        private TitleTranslator $translator,
    ) {
        //
    }

    public function compose(View $view): void
    {
        $data = $view->getData();

        if (! isset($data['title']) || ! is_string($data['title'])) {
            return;
        }

        $view->with('title', $this->translator->translate($data['title']));
    }
}
