<?php

namespace Azuriom\Plugin\ExtendedTranslation\Models;

use Azuriom\Models\NavbarElement;
use Azuriom\Models\Traits\HasTablePrefix;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $navbar_element_id
 * @property string $locale
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\NavbarElement $element
 */
class NavbarElementTranslation extends Model
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
        'navbar_element_id',
        'locale',
        'name',
    ];

    /**
     * Get the navbar element this translation belongs to.
     */
    public function element(): BelongsTo
    {
        return $this->belongsTo(NavbarElement::class, 'navbar_element_id');
    }
}
