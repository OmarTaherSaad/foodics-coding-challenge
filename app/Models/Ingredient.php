<?php

namespace App\Models;

use App\Notifications\IngredientLowStockNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Notification;

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
        'default_stock_in_grams',
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


    /**
     * Notify the merchant if the stock is below 50% and not already notified.
     */
    public function checkAndNotifyLowStock()
    {
        // Check if the stock is below 50% of the default stock and if the notification has not been sent
        if ($this->stock_in_grams < ($this->default_stock_in_grams / 2) && !$this->notified_for_restock_sent) {
            // Set the restock_needed flag to true to indicate that restocking is needed
            $this->restock_needed = true;
            // For the purpose of this example, we will send the notification via email to a predefined merchant email
            Notification::route('mail', config('mail.merchant_email'))
                ->notify(new IngredientLowStockNotification($this));
            // Set the notified_for_restock_sent flag to true to avoid sending multiple notifications
            $this->notified_for_restock_sent = true;
            $this->save();
        }
    }
}
