<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('type');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'user_type' => $user->type ? $user->type->name : null,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
            $user->save();
        }
        return response()->json([
            'message' => 'Cập nhật thành công',
            'updated_at' => $user->updated_at,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác'],
            ]);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
