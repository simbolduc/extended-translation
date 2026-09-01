<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\PluginOptions;
use Azuriom\Plugin\Vote\Models\Reward;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminRewardsComposer
{
    public function __construct(
        private LocaleCatalog $locales,
        private RewardTranslator $translator,
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
            'indexUrl' => route('extended-translation.admin.vote.index'),
        ];

        if (isset($data['reward']) && $data['reward'] instanceof Reward) {
            $payload['editUrl'] = $this->editUrl($data['reward']);
        }

        if (isset($data['rewards'])) {
            $payload['rewards'] = Collection::make($data['rewards'])
                ->filter(fn ($reward) => $reward instanceof Reward)
                ->mapWithKeys(fn (Reward $reward) => [$reward->id => $this->editUrl($reward)])
                ->all();
        }

        $view->getFactory()->startPush(
            'footer-scripts',
            view('extended-translation::admin.vote.inject', ['payload' => $payload])->render()
        );
    }

    protected function editUrl(Reward $reward): string
    {
        $existing = $this->translator->allByReward()->get($reward->id)?->keys() ?? collect();

        return route('extended-translation.admin.vote.edit', [
            $reward,
            $this->locales->firstTargetLocale($existing),
        ]);
    }
}
