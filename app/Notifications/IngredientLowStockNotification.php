<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ingredient;

class IngredientLowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // For the purpose of this example, we will use the application URL as the restock URL
        $restockUrl = url('/');
        return (new MailMessage)
            ->subject('Ingredient Low Stock Alert')
            ->line('The stock for ' . $this->ingredient->name . ' is below 50%.')
            ->action('Restock Now', $restockUrl)
            ->line('Thank you for using our application!');
    }
}
