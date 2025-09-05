<?php

namespace App\Http\Controllers;

use App\DTO\Shared\BaseFiltersDTO;
use App\DTO\Activity\ActivityFiltersDTO;
use App\DTO\User\UpdateOrCreateDTO;
use App\Http\Requests\Activity\ActivitiesRequest;
use App\Http\Requests\Shared\BaseFilterRequest;
use App\Http\Requests\User\BulkDeleteRequest;
use App\Http\Requests\User\BulkUpdateRequest;
use App\Http\Requests\User\ChangePasswordAsAdminRequest;
use App\Http\Requests\User\ChangePinAsAdminRequest;
use App\Http\Requests\User\UpdateOrCreateRequest;
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

    public function generateNonExistingPin(UserService $userService) : JsonResponse
    {
        $newPin = $userService->generateNonExistingPin();
        return response()->json(['pin' => $newPin], 200);
    }

    public function list(BaseFilterRequest $request, UserService $userService) : JsonResponse
    {
        $paginatedUsers = $userService->getPaginatedUsers(
            BaseFiltersDTO::from($request->validated())
        );

        return response()->json($paginatedUsers);
    }

    public function updateOrCreate(UpdateOrCreateRequest $request, UserService $userService) : JsonResponse
    {
        try {
            $request->validatePIN();
            $user = $userService->updateOrCreate(
                UpdateOrCreateDTO::from($request->validated())
            );
            
            return response()->json(['message' => $user->first_name . ($user->wasRecentlyCreated ? ' blev oprettet som bruger' : ' blev opdateret')], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under oprettelse af bruger"], 500);
        }
    }

    public function bulkUpdate(BulkUpdateRequest $request, UserService $userService) : JsonResponse
    {
        try {
            $userService->bulkUpdate($request->input('userIds'), $request->input('accesses'));
            return response()->json(['message' => 'Brugerene blev opdateret'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under opdatering af brugere"], 500);
        }
    }

    public function bulkDelete(BulkDeleteRequest $request, UserService $userService) : JsonResponse
    {
        try {
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

    public function activities(int $id, ActivitiesRequest $request, ActivityService $activityService) : JsonResponse
    {
        $activities = $activityService->getUserActivities($id, ActivityFiltersDTO::from($request->validated()));

        return response()->json($activities);
    }

    public function adminUpdatePassword(int $id, ChangePasswordAsAdminRequest $request, UserService $userService) : JsonResponse
    {
        try {
            $userService->adminUpdatePassword($id, $request->input('password'));
            return response()->json(['message' => 'Brugerens password blev opdateret'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under opdatering af brugerens password"], 500);
        }
    }

    public function adminUpdatePin(int $id, ChangePinAsAdminRequest $request, UserService $userService) : JsonResponse
    {
        try {
            $request->validatePIN();
            $userService->adminUpdatePin($id, $request->input('pin'));
            return response()->json(['message' => 'Brugerens pin blev opdateret'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Der skete en kritisk fejl under opdatering af brugerens pin"], 500);
        }
    }
}
