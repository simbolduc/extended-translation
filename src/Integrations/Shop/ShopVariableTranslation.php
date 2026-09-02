<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Shop\Models\Variable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shop_variable_id
 * @property string $locale
 * @property string $description
 * @property array|null $options
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Shop\Models\Variable $variable
 */
class ShopVariableTranslation extends Model
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
        'shop_variable_id',
        'locale',
        'description',
        'options',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the shop variable this translation belongs to.
     */
    public function variable(): BelongsTo
    {
        return $this->belongsTo(Variable::class, 'shop_variable_id');
    }

    public function isStale(?Variable $variable = null): bool
    {
        $variable ??= $this->relationLoaded('variable') ? $this->variable : null;

        if ($variable === null || $variable->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $variable->updated_at->gt($this->updated_at);
    }
}
