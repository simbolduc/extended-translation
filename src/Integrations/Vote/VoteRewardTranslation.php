<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Vote\Models\Reward;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vote_reward_id
 * @property string $locale
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Vote\Models\Reward $reward
 */
class VoteRewardTranslation extends Model
{
    use HasTablePrefix;

    /**
     * The table prefix associated with the model.
     */
    protected string $prefix = 'extended_translation_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vote_reward_id',
        'locale',
        'name',
    ];

    /**
     * Get the vote reward this translation belongs to.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class, 'vote_reward_id');
    }

    public function isStale(?Reward $reward = null): bool
    {
        $reward ??= $this->relationLoaded('reward') ? $this->reward : null;

        if ($reward === null || $reward->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $reward->updated_at->gt($this->updated_at);
    }
}
