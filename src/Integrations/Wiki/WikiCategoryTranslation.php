<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Wiki\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $wiki_category_id
 * @property string $locale
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Wiki\Models\Category $category
 */
class WikiCategoryTranslation extends Model
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
        'wiki_category_id',
        'locale',
        'name',
    ];

    /**
     * Get the wiki category this translation belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'wiki_category_id');
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
