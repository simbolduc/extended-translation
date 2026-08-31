<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Wiki;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $wiki_page_id
 * @property string $locale
 * @property string $title
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Wiki\Models\Page $page
 */
class WikiPageTranslation extends Model
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
        'wiki_page_id',
        'locale',
        'title',
        'content',
    ];

    /**
     * Get the wiki page this translation belongs to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'wiki_page_id');
    }

    public function isStale(?Page $page = null): bool
    {
        $page ??= $this->relationLoaded('page') ? $this->page : null;

        if ($page === null || $page->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $page->updated_at->gt($this->updated_at);
    }
}
