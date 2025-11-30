<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // Check nếu tìm thấy người dùng với username như trên và mật khẩu khớp
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Sai tên đăng nhập hoặc mật khẩu'], 401);
        }

        // Check nếu người dùng bị khóa
        if ($user->status == 'inactive' || $user->status == 'suspended') {
            return response()->json(['message' => 'Tài khoản của bạn đã bị khóa'], 403);
        }

        Auth::login($user);

        return response()->json(
            [
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                ]
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }
}
