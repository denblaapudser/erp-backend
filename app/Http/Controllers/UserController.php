<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function authenticatedUser(Request $request)
    {
        return $request->user();
    }

    public function list(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'perPage' => 'nullable|integer|min:1|max:100',
        ]);
        
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        return $query->paginate($request->perPage ?? 20);
    }

    public function updateOrCreate(Request $request)
    {
        try {
                $data = $request->validate([
                'id' => 'sometimes|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => "required|email|max:255|unique:users,email,{$request->id}",
                'password' => 'nullable|string|min:8',
                'accesses' => 'nullable|array',
            ]);

            $insert = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $insert['password'] = bcrypt($data['password']);
            }

            $user = User::updateOrCreate(
                ['id' => $data['id'] ?? null],
                $insert
            );
            $user->accesses()->sync($data['accesses'] ?? []);
            return response()->json(['message' => $user->first_name . ($user->wasRecentlyCreated ? ' blev oprettet som bruger' : ' blev opdateret')], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under oprettelse af bruger"], 500);
        }
    }

    public function bulkUpdate(Request $request){
        try {
            $users = User::findOrFail($request->userIds);

            foreach($users as $user){
                $user->accesses()->sync($request->accesses);
                $user->save();
            }
            return response()->json(['message' => 'Brugerene blev opdateret'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under opdatering af brugere"], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $userIds = $request->userIds;
            User::whereIn('id', $userIds)->delete();
            return response()->json(['message' => 'Brugere slettet'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under sletning af brugere"], 500);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'Bruger slettet'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under sletning af bruger"], 500);
        }
    }
}
