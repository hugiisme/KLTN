<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Lấy danh sách user thuộc 1 organization
    public function getByOrg($orgId)
    {
        $org = Organization::findOrFail($orgId);

        return response()->json(
            $org->users()
                ->select('users.id', 'users.username', 'users.user_type_id', 'users.status')
                ->with('type')
                ->get()
        );
    }

    // Tạo user + gán vào organization
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
            'user_type_id' => 'required|exists:user_types,id',
            'status' => 'nullable|in:active,inactive,suspended',
            'org_id' => 'required|exists:organizations,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password_hash' => bcrypt($request->password),
            'user_type_id' => $request->user_type_id,
            'status' => $request->status ?? 'active',
        ]);

        $user->organizations()->attach($request->org_id);

        return response()->json($user, 201);
    }

    // Cập nhật user
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'user_type_id' => 'nullable|exists:user_types,id',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $user = User::findOrFail($id);

        $user->update($request->only(['username', 'user_type_id', 'status']));

        return response()->json($user);
    }

    // Xoá user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // Đặt lại mật khẩu user
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->password_hash = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password reset successful']);
    }
}
