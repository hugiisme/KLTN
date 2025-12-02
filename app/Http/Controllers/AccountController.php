<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class AccountController extends BaseController
{
    public function __construct()
    {
        // Use session-based auth for SPA requests (cookies). If you use Sanctum
        // with stateful SPA auth, change to 'auth:sanctum' and ensure
        // /sanctum/csrf-cookie flow is implemented on the frontend.
        $this->middleware('auth');
    }


    public function me(Request $request)
    {
        $user = $request->user();

        // If there's no authenticated user, return 401 so the frontend
        // can treat this as unauthenticated and redirect to login.
        if (!$user) {
            Log::info('Unauthenticated request to AccountController@me');
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        Log::info('User info', $user->toArray());

        return response()->json([
            'username'   => $user->username ?? 'no_username',
            'user_type'  => $user->user_type ?? 'no_type',
            'status'     => $user->status ?? 'no_status',
            'created_at' => $user->created_at ?? null,
            'updated_at' => $user->updated_at ?? null,
        ]);
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data = $request->validate([
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Cập nhật thành công',
            'updated_at' => $user->updated_at,
        ]);
    }


    public function changePassword(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không đúng'], 400);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }
}
