<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function activities(ActivityService $activityService)
    {
        return $activityService->getAll();
    }

    public function getAvailableActivityFilters(Request $request, ActivityService $activityService)
    {
        return $activityService->getAvailableActivityFilters($request->input('context'));
    }
}
