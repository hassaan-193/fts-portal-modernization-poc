<?php

namespace App\Http\Controllers\API\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForemanAuthController extends Controller
{
    /**
     * Login endpoint
     *
     * POST /api/v1/foreman/login
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Generate api_token
            $token = Str::random(80);
            $user->update(['api_token' => $token]);

            // Get user roles
            $roles = $user->getRoleNames(); // Spatie method to get all roles

            Log::info('User logged in: ' . $user->email . ' with roles: ' . $roles->implode(', '));

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles, // Return roles
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Login failed',
            ], 500);
        }
    }

    /**
     * Logout endpoint
     *
     * POST /api/v1/foreman/logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->update(['api_token' => null]);

            Log::info('User logged out: ' . $request->user()->email);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * Refresh token endpoint
     *
     * POST /api/v1/foreman/refresh-token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            $token = Str::random(80);
            $user->update(['api_token' => $token]);

            Log::info('Token refreshed for: ' . $user->email);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $token
            ], 200);

        } catch (\Exception $e) {
            Log::error('refreshToken error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token'
            ], 500);
        }
    }
}