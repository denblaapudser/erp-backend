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
        'note',
        'restock_url',
        'image_id',
    ];

    protected $casts = [
        'should_alert' => 'boolean',
    ];

    protected $with = [
        'image',
    ];

    protected $appends = [
        'image_url',
    ];

    protected static function booted()
    {
        static::updated(function ($product) {
            // Only send notification if qty just crossed the threshold
            if (
                $product->should_alert &&
                $product->qty <= $product->alert_threshold &&
                $product->getOriginal('qty') > $product->alert_threshold
            ) {
                $users = User::recipientsForNotificationType(LowInventoryStockAlert::class)->get();
                Notification::send($users, new LowInventoryStockAlert($product));
            }
        });
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image?->url;
    }
}
