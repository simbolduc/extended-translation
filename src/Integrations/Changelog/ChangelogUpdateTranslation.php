<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Changelog\Models\Update;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $changelog_update_id
 * @property string $locale
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Changelog\Models\Update $update
 */
class ChangelogUpdateTranslation extends Model
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
        'changelog_update_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Get the changelog update this translation belongs to.
     */
    public function updateModel(): BelongsTo
    {
        return $this->belongsTo(Update::class, 'changelog_update_id');
    }

    public function isStale(?Update $update = null): bool
    {
        $update ??= $this->relationLoaded('updateModel') ? $this->updateModel : null;

        if ($update === null || $update->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $update->updated_at->gt($this->updated_at);
    }
}
