<?php

namespace App\Models;

use App\Enums\ActivityEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_data',
        'subject_id',
        'subject_type',
        'activity_label',
    ];

    protected $casts = [
        'activity_data' => 'object',
    ];

    protected $appends = [
        'subject_label',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId): void
    {
        $query->where('user_id', $userId);
    }

    public function scopeForProduct($query, string $productID): void
    {
        $query->where('subject_type', InventoryProducts::class)
            ->where('subject_id', $productID);
    }
    
    public function scopeApplyFilters($query, object $filters): void
    {
        $query
            ->when($filters->type, fn($query) => $query->whereIn('activity_type', $filters->type))
            ->when(
                ($filters->from ?? null) && ($filters->to ?? null),
                fn($query) => $query->whereDate('created_at', '>=', $filters->from)
                                    ->whereDate('created_at', '<=', $filters->to)
            )
            ->when($filters->search ?? null, function($query) use ($filters) {
                $query->where(function($q) use ($filters) {
                    $q->where('activity_type', 'like', "%{$filters->search}%")
                      ->orWhere('activity_label', 'like', "%{$filters->search}%");
                });
            });
    }

    public function getSubjectLabelAttribute(): string
    {
        return match ($this->subject_type) {
            User::class => 'Bruger',
            InventoryProducts::class => 'Produkt',
            default => 'Andet',
        };
    }

    public function scopeByContext($query, string $context): void
    {
        if($context === 'user') {
            $query->where('subject_type', User::class);
        } elseif ($context === 'product') {
            $query->where('subject_type', InventoryProducts::class);
        } else {
            $query->whereNotIn('subject_type', [User::class, InventoryProducts::class]);
        }
    }
}
