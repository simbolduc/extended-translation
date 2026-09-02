<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Shop\Models\Offer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shop_offer_id
 * @property string $locale
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Shop\Models\Offer $offer
 */
class ShopOfferTranslation extends Model
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
        'shop_offer_id',
        'locale',
        'name',
    ];

    /**
     * Get the shop offer this translation belongs to.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'shop_offer_id');
    }

    public function isStale(?Offer $offer = null): bool
    {
        $offer ??= $this->relationLoaded('offer') ? $this->offer : null;

        if ($offer === null || $offer->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $offer->updated_at->gt($this->updated_at);
    }
}
