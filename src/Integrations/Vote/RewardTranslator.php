<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Azuriom\Plugin\ExtendedTranslation\Core\Locale\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Core\Support\SkipsAdminOverlay;
use Azuriom\Plugin\Vote\Models\Reward;
use Illuminate\Support\Collection;
use WeakMap;

class RewardTranslator
{
    use SkipsAdminOverlay;

    /**
     * @var Collection<int, Collection<string, VoteRewardTranslation>>|null
     */
    private ?Collection $translations = null;

    /**
     * @var WeakMap<Reward, array{name: string}>
     */
    private WeakMap $originals;

    public function __construct(
        private LocaleCatalog $locales,
    ) {
        $this->originals = new WeakMap();
    }

    /**
     * Replace the vote reward's visible name with the translation for the current locale.
     */
    public function apply(Reward $reward): void
    {
        if (! $this->shouldApply()) {
            return;
        }

        if (! isset($this->originals[$reward])) {
            $this->originals[$reward] = [
                'name' => (string) ($reward->getAttributes()['name'] ?? ''),
            ];
        }

        $original = $this->originals[$reward]['name'];
        $translation = $this->forReward($reward, $this->locales->current());
        $value = $translation?->name;

        $reward->setAttribute(
            'name',
            is_string($value) && $value !== '' ? $value : $original
        );
        $reward->syncOriginalAttribute('name');
    }

    private function forReward(Reward $reward, ?string $locale = null): ?VoteRewardTranslation
    {
        $candidates = $this->locales->candidates($locale);
        $byLocale = $this->allByReward()->get($reward->id);

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
     * @return Collection<int, Collection<string, VoteRewardTranslation>>
     */
    public function allByReward(): Collection
    {
        return $this->translations ??= VoteRewardTranslation::query()
            ->get()
            ->groupBy('vote_reward_id')
            ->map(fn (Collection $group) => $group->keyBy('locale'));
    }

    public function forgetCache(): void
    {
        $this->translations = null;
    }
}
