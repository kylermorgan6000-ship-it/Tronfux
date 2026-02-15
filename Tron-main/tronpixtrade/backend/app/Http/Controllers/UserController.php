<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateProfileImageRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * List all users (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->forbidden('Only admins can view user list');
            }

            $query = User::query();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }

            if ($request->has('sort')) {
                $direction = $request->has('direction') && $request->direction === 'desc' ? 'desc' : 'asc';
                $query->orderBy($request->sort, $direction);
            } else {
                $query->latest();
            }

            $users = $query->paginate($request->per_page ?? 15);

            return $this->successWithPagination($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user profile
     */
    public function show($id): JsonResponse
    {
        try {
            // Can only view own profile or admin can view any
            if (auth()->id() !== (int) $id && !auth()->user()->is_admin) {
                return $this->forbidden('You can only view your own profile');
            }

            $user = User::find($id);

            if (!$user) {
                return $this->notFound('User not found');
            }

            return $this->success(['user' => $user], 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update user profile
     */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->notFound('User not found');
            }

            // Authorization check
            if (auth()->id() !== (int) $id && !auth()->user()->is_admin) {
                return $this->forbidden('You can only update your own profile');
            }

            $user->update($request->only(['name', 'email', 'phone', 'country', 'birth_date']));

            return $this->success(['user' => $user], 'User updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update profile image
     */
    public function updateProfileImage(UpdateProfileImageRequest $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return $this->notFound('User not found');
            }

            // Delete old image if exists
            if ($user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))) {
                unlink(storage_path('app/public/' . $user->profile_image));
            }

            // Store new image
            $path = $request->file('image')->store('profile_images', 'public');
            $user->update(['profile_image' => $path]);

            return $this->success(['user' => $user], 'Profile image updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update profile image: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update own profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $user->update($request->only(['name', 'email']));
            return $this->success(['user' => $user], 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = auth()->user();

            if (!\Hash::check($request->current_password, $user->password)) {
                return $this->error('Current password is incorrect', 422);
            }

            $user->update(['password' => \Hash::make($request->password)]);

            return $this->success([], 'Password changed successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to change password: ' . $e->getMessage(), 500);
        }
    }
}
