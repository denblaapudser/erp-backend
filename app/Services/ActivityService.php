<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivityService
{
        public function getUserActivities(int $userId, array $filters = []) : Collection|LengthAwarePaginator
    {
        if(!empty($filters)){
            $filters = (object)$filters;
            $filters->type = !empty($filters->type) ? collect(explode(',', $filters->type)) : [null];
            $filters->subject = !empty($filters->subject) ? collect(explode(',', $filters->subject)) : [null];
            $filters->search ??= null;
            $filters->from ??= null;
            $filters->to ??= null;
        }

        $query = Activity::forUser($userId)
            ->when($filters, fn($query, $filters) => $query->applyFilters($filters))
            ->latest();

        $activities = ($filters->perPage ?? null) ?
            $query->paginate($filters->perPage) :
            $query->get();

        return $activities;
    }

    public function getProductActivities(int $productId, array $filters = []) : Collection|LengthAwarePaginator
    {
        if(!empty($filters)){
            $filters = (object)$filters;
            $filters->type = !empty($filters->type) ? collect(explode(',', $filters->type)) : [null];
            $filters->subject = !empty($filters->subject) ? collect(explode(',', $filters->subject)) : [null];
            $filters->search ??= null;
            $filters->from ??= null;
            $filters->to ??= null;
        }

        $query = Activity::forProduct($productId)
            ->when($filters, fn($query, $filters) => $query->applyFilters($filters))
            ->latest();

        $activities = ($filters->perPage ?? null) ?
            $query->paginate($filters->perPage) :
            $query->get();

        return $activities;
    }

    public function getAvailableActivityFilters() : Collection
    {
        $availableFilters = Activity::select('activity_type', 'subject_type')
            ->distinct()
            ->get()
            ->map(fn($item) => [
                'type' => $item->activity_type,
                'subject_type' => $item->subject_type,
                'label' => $item->subject_label,
            ]);
        return $availableFilters;
    }
}