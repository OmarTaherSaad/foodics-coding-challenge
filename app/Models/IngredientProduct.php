<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientProduct extends Pivot
{
    protected $table = 'ingredient_product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity_in_grams',
    ];
}
