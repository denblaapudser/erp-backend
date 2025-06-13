<?php

namespace App\Models;

use App\Notifications\LowInventoryStockAlert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class InventoryProducts extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryProductsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'qty',
        'should_alert',
        'alert_threshold',
    ];

    protected static function booted()
    {
        // Trigger notification when product is updated and stock is lower than or equal to the alert threshold
        static::updated(function ($product) {
            if ($product->should_alert && $product->qty <= $product->alert_threshold) {
                $product->sendLowStockAlert();
            }
        });
    }

    public function sendLowStockAlert() : void
    {
        $users = User::recipientsForNotificationType(LowInventoryStockAlert::class)->get();
        Notification::send($users, new LowInventoryStockAlert($this));
    }
}
