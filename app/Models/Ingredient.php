<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'stock_in_grams',
        'restock_needed',
        'notified_for_restock_sent',
    ];

    #region Eloquent Relationships

    /**
     * Get the products for the ingredient.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(IngredientProduct::class)
            ->withPivot('quantity_in_grams')
            ->withTimestamps();
    }

    #endregion
}
