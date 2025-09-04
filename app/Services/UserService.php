<?php

namespace App\Services;

use App\Events\User\BulkDeletedEvent;
use App\Events\User\BulkUpdatedEvent;
use App\Events\User\CreatedEvent;
use App\Events\User\DeletedEvent;
use App\Events\User\UpdatedEvent;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getPaginatedUsers($perPage = 20, $search = null) : LengthAwarePaginator
    {
        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }


    public function updateOrCreate($name, $username, $email = null, $password = null, $pin = null, $accesses = [], $id = null) : User
    {
        $user = User::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'pin' => $pin,
                'password' => $password,
            ]
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

    public function authenticateAsAdmin(string $username, ?string $password) : User
    {
        $user = User::where('username', $username)->orWhere('email', $username)->first();

        if(!$user) {
            throw ValidationException::withMessages(['Creds' => 'Forkert email, brugernavn eller adgangskode for administrator.']);
        }

        if(!$user || ! Hash::check($password, (string)$user->password)) {
            throw ValidationException::withMessages(['Creds' => 'Forkert email, brugernavn eller adgangskode for administrator.']);
        }

        if(!$user->hasAccess('adminAccess')) {
            throw ValidationException::withMessages(['no_admin_access' => 'Du har forsøget at logge ind som administrator men du har ikke administrator adgang.']);
        } 

        return $user;
    }

    public function authenticateAsEmployee(string $pin) : User
    {
        $user = User::where('pin', $pin)->first();

        if(!$user) {
            throw ValidationException::withMessages(['wrong_pin' => 'Forkert PIN for medarbejder.']);
        }

        return $user;
    }
}