<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core\Posts;

use Azuriom\Models\Post;
use Azuriom\Models\Traits\HasTablePrefix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property string $locale
 * @property string $title
 * @property string $description
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\Post $post
 */
class PostTranslation extends Model
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
        'post_id',
        'locale',
        'title',
        'description',
        'content',
    ];

    /**
     * Get the post this translation belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function isStale(?Post $post = null): bool
    {
        $post ??= $this->relationLoaded('post') ? $this->post : null;

        if ($post === null || $post->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $post->updated_at->gt($this->updated_at);
    }
}
