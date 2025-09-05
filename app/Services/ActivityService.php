<?php

namespace App\Services;

use App\DTO\Activity\ActivityFiltersDTO;
use App\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivityService
{
    public function getAll() : Collection
    {
        return Activity::with('user')->latest()->get();
    }

    public function getUserActivities(int $userId, ActivityFiltersDTO $filters = null) : Collection|LengthAwarePaginator
    {
        $query = Activity::forUser($userId)
            ->when($filters, fn($query, $filters) => $query->applyFilters($filters))
            ->latest();

        $activities = ($filters->perPage ?? null) ?
            $query->paginate($filters->perPage) :
            $query->get();

        return $activities;
    }

    public function getProductActivities(int $productId, ActivityFiltersDTO $filters = null) : Collection|LengthAwarePaginator
    {
        $query = Activity::with('user')->forProduct($productId)
            ->when($filters, fn($query, $filters) => $query->applyFilters($filters))
            ->latest();

        $activities = ($filters->perPage ?? null) ?
            $query->paginate($filters->perPage) :
            $query->get();

        return $activities;
    }

    public function getAvailableActivityFilters($context = null) : Collection
    {
        $filters = Activity::when($context, fn($q) => $q->byContext($context))
            ->with('user')
            ->get();

        $test = collect([
            'availableTypes' => $filters->pluck('activity_type')->unique()->filter()->values(),
            'availableSubjects' => $filters->map(fn($item) => [
                'type' => $item->subject_type,
                'label' => $item->subject_label,
            ])->unique(fn($item) => $item['type'].'-'.$item['label'])->values(),
                'responsibleUsers' => $filters->pluck('user')->filter()->unique('id')->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
            ])->values(),
        ]);
        return $test;
    }
}