<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $request->session()->regenerate();

        Log::info('=== LOGIN SUCCESS ===');
        Log::info('User ID logged in: ' . Auth::id());
        Log::info('Generated Session ID: ' . $request->session()->getId());
        Log::info('Session Config Domain: ' . config('session.domain'));

        // (Tùy chọn) Nếu bạn muốn lưu thêm thông tin tùy chỉnh vào session
        // $request->session()->put('key', 'value');

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

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }
}
