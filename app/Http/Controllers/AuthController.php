<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
            return $this->errorResponse('Sai tên đăng nhập hoặc mật khẩu', 401);
        }

        if ($user->status == 'inactive' || $user->status == 'suspended') {
            return $this->errorResponse('Tài khoản của bạn đã bị khóa', 403);
        }

        Auth::login($user);
        $request->session()->regenerate();

        Log::info('=== LOGIN SUCCESS ===');
        Log::info('User ID logged in: ' . Auth::id());
        Log::info('Generated Session ID: ' . $request->session()->getId());

        return $this->successResponse([
            'id' => $user->id,
            'username' => $user->username,
        ], 'Đăng nhập thành công');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successMessage('Đăng xuất thành công');
    }
}
