<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\InventoryProducts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ImageCleanup implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $productImages = InventoryProducts::whereNotNull('image_id')->pluck('image_id');
        Image::whereNotIn('id', $productImages)->get()->each(fn($image) => $image->delete());
    }
}
