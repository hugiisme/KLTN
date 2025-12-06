<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponse;

class AccountController extends Controller
{
    use ApiResponse;

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse('Chưa xác thực', 401);
        }

        $user->load('type');

        return $this->successResponse([
            'id' => $user->id,
            'username' => $user->username,
            'user_type' => $user->type ? $user->type->name : null,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'Thông tin tài khoản của bạn');
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

        return $this->successMessage('Cập nhật tài khoản thành công');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác'],
            ]);
        }

        $user->password_hash = Hash::make($data['new_password']);
        $user->save();

        return $this->successMessage('Đổi mật khẩu thành công');
    }
}
