<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Pages;

use Azuriom\Models\Page;
use Azuriom\Models\Traits\HasTablePrefix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $page_id
 * @property string $locale
 * @property string $title
 * @property string $description
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\Page $page
 */
class PageTranslation extends Model
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
        'page_id',
        'locale',
        'title',
        'description',
        'content',
    ];

    /**
     * Get the page this translation belongs to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
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
