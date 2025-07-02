<?php

use App\Jobs\ImageCleanup;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


Schedule::job(new ImageCleanup())->daily()->at('02:00')->name('image.cleanup');