<?php

namespace App\Services;

use App\Events\User\BulkDeletedEvent;
use App\Events\User\BulkUpdatedEvent;
use App\Events\User\CreatedEvent;
use App\Events\User\DeletedEvent;
use App\Events\User\UpdatedEvent;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function getPaginatedUsers($perPage = 20, $search = null) : LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }


    public function updateOrCreate($name, $email, $password = null, $accesses = [], $id = null) : User
    {
        $insert = [
            'name' => $name,
            'email' => $email,
        ];
        
        if (!empty($password)) {
            $insert['password'] = bcrypt($password);
        }

        $user = User::updateOrCreate(
            ['id' => $id],
            $insert
        );
        $user->accesses()->sync($accesses);

        if ($user->wasRecentlyCreated) {
            CreatedEvent::dispatch($user);
        } else {
            UpdatedEvent::dispatch($user);
        }

        return $user;
    }

    public function bulkUpdate(array $userIds, array $accesses = null) : void
    {
        $users = User::findOrFail($userIds);

        if ($accesses) {
            foreach($users as $user){
                $user->accesses()->sync($accesses);
                $user->save();
            }
        } 

        BulkUpdatedEvent::dispatch($users->pluck('id'));
    }

    public function bulkDelete(array $userIds) : void
    {
        $users = User::whereIn('id', $userIds)->get();
        User::whereIn('id', $userIds)->delete();
        BulkDeletedEvent::dispatch($users);
    }

    public function delete(int $id) : void
    {
        $user = User::findOrFail($id);
        $user->delete();
        DeletedEvent::dispatch($user);
    }
}