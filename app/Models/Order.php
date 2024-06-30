<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    #region Eloquent Relationships

    /**
     * Get the products for the order.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    #endregion
}
