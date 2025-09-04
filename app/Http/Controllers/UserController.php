<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function authenticatedUser(Request $request) : JsonResponse
    {
        return response()->json($request->user());
    }

    public function list(Request $request, UserService $userService) : JsonResponse
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'perPage' => 'nullable|integer|min:1|max:100',
        ]);

        $paginatedUsers = $userService->getPaginatedUsers(
            $request->input('perPage'),
            $request->input('search')
        );

        return response()->json($paginatedUsers);
    }

    public function updateOrCreate(Request $request, UserService $userService) : JsonResponse
    {
        try {
                $request->validate([
                'id' => 'sometimes|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => "nullable|email|max:255|unique:users,email,{$request->id}",
                'password' => 'nullable|string|min:8',
                'accesses' => 'nullable|array',
                'pin' => 'nullable|string|max:4',
                'username' => "nullable|string|max:255|unique:users,username,{$request->id}",
            ]);

            if ($request->has('pin') && !is_numeric($request->input('pin'))) {
                return response()->json(['message' => 'PIN skal være et tala'], 422);
            }

            $user = $userService->updateOrCreate(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $request->input('accesses', []),
                $request->input('id', null)
            );
            
            return response()->json(['message' => $user->first_name . ($user->wasRecentlyCreated ? ' blev oprettet som bruger' : ' blev opdateret')], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under oprettelse af bruger"], 500);
        }
    }

    public function bulkUpdate(Request $request, UserService $userService) : JsonResponse
    {
        try {
            $request->validate([
                'userIds' => 'required|array',
                'userIds.*' => 'exists:users,id',
                'accesses' => 'nullable|array',
            ]);

            $userService->bulkUpdate($request->input('userIds'), $request->input('accesses'));

            return response()->json(['message' => 'Brugerene blev opdateret'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under opdatering af brugere"], 500);
        }
    }

    public function bulkDelete(Request $request, UserService $userService) : JsonResponse
    {
        try {
            $request->validate([
                'userIds' => 'required|array',
                'userIds.*' => 'exists:users,id',
            ]);

            $userService->bulkDelete($request->input('userIds'));
            
            return response()->json(['message' => 'Brugere slettet'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under sletning af brugere"], 500);
        }
    }

    public function delete(int $id, UserService $userService) : JsonResponse
    {
        try {
            $userService->delete($id);

            return response()->json(['message' => 'Bruger slettet'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under sletning af bruger"], 500);
        }
    }

    public function activities(int $id, Request $request, ActivityService $activityService) : JsonResponse
    {
        $filters = $request->validate([
            'type' => 'nullable|string',
            'subject' => 'nullable|string',
            'search' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'perPage' => 'nullable|integer',
        ]);

        $activities = $activityService->getUserActivities($id, $filters);

        return response()->json($activities);
    }
}
