<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Changelog;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Changelog\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $changelog_category_id
 * @property string $locale
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Changelog\Models\Category $category
 */
class ChangelogCategoryTranslation extends Model
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
        'changelog_category_id',
        'locale',
        'name',
    ];

    /**
     * Get the changelog category this translation belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'changelog_category_id');
    }

    public function isStale(?Category $category = null): bool
    {
        $category ??= $this->relationLoaded('category') ? $this->category : null;

        if ($category === null || $category->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $category->updated_at->gt($this->updated_at);
    }
}
