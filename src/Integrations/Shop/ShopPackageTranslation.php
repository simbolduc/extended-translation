<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Shop;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Plugin\Shop\Models\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shop_package_id
 * @property string $locale
 * @property string $name
 * @property string $short_description
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Plugin\Shop\Models\Package $package
 */
class ShopPackageTranslation extends Model
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
        'shop_package_id',
        'locale',
        'name',
        'short_description',
        'description',
    ];

    /**
     * Get the shop package this translation belongs to.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'shop_package_id');
    }

    public function isStale(?Package $package = null): bool
    {
        $package ??= $this->relationLoaded('package') ? $this->package : null;

        if ($package === null || $package->updated_at === null || $this->updated_at === null) {
            return false;
        }

        return $package->updated_at->gt($this->updated_at);
    }
}
