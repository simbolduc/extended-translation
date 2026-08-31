<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\FAQ\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $question_id
 * @property string $locale
 * @property string $name
 * @property string $answer
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\FAQ\Models\Question $question
 */
class QuestionTranslation extends Model
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
        'question_id',
        'locale',
        'name',
        'answer',
    ];

    /**
     * Get the FAQ question this translation belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function isStale(?Question $question = null): bool
    {
        $question ??= $this->relationLoaded('question') ? $this->question : null;

        if ($question === null || $question->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $question->updated_at->gt($this->updated_at);
    }
}
