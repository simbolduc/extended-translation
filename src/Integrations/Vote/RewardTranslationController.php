<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\Vote\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RewardTranslationController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
        private RewardTranslator $translator,
    ) {
        //
    }

    /**
     * Display the vote rewards that can be translated.
     */
    public function index(): View
    {
        $rewards = Reward::query()
            ->orderBy('id')
            ->get();

        return view('extended-translation::admin.vote.index', [
            'rewards' => $rewards,
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translations' => $this->translator->allByReward(),
        ]);
    }

    /**
     * Show the form for translating a vote reward.
     */
    public function edit(Reward $voteReward, string $locale): View
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        $translation = VoteRewardTranslation::query()
            ->where('vote_reward_id', $voteReward->id)
            ->where('locale', $locale)
            ->first();

        $existing = VoteRewardTranslation::query()
            ->where('vote_reward_id', $voteReward->id)
            ->pluck('locale');

        return view('extended-translation::admin.vote.edit', [
            'reward' => $voteReward,
            'locale' => $locale,
            'localeName' => $this->locales->name($locale),
            'locales' => $this->locales->enabled(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'translation' => $translation,
            'existing' => $existing,
        ]);
    }

    /**
     * Store or update a vote reward translation.
     */
    public function update(RewardTranslationRequest $request, Reward $voteReward, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        VoteRewardTranslation::query()->updateOrCreate(
            [
                'vote_reward_id' => $voteReward->id,
                'locale' => $locale,
            ],
            $request->validated()
        );

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.vote.updated', $voteReward, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.vote.edit', [$voteReward, $locale])
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove a vote reward translation.
     */
    public function destroy(Reward $voteReward, string $locale): RedirectResponse
    {
        abort_unless($this->locales->isEnabled($locale), 404);

        VoteRewardTranslation::query()
            ->where('vote_reward_id', $voteReward->id)
            ->where('locale', $locale)
            ->delete();

        $this->translator->forgetCache();

        ActionLog::log('extended-translation.vote.deleted', $voteReward, [
            'locale' => $locale,
        ]);

        return to_route('extended-translation.admin.vote.edit', [$voteReward, $locale])
            ->with('success', trans('messages.status.success'));
    }
}
