<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = User::with('type');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('username', 'like', "%{$search}%");
        }

        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $query->where($request->filter_field, $request->filter_value);
        }

        if ($request->filled('sort_field')) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_direction ?? 'asc'
            );
        }

        $perPage = (int) ($request->per_page ?? 10);
        $items = $query->paginate($perPage);

        return $this->paginatedResponse($items, 'Danh sách người dùng');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
            'user_type_id' => 'required|exists:user_types,id',
            'status' => 'nullable|in:active,inactive,suspended',
            'org_id' => 'required|exists:organizations,id',
        ]);

        $user = User::create([
            'username' => $data['username'],
            'password_hash' => bcrypt($data['password']),
            'user_type_id' => $data['user_type_id'],
            'status' => $data['status'] ?? 'active',
        ]);

        $user->organizations()->attach($data['org_id']);
        $user->load('type', 'organizations');

        return $this->successResponse($user, 'Tạo người dùng thành công', 201);
    }

    public function show($id)
    {
        $user = User::with('type', 'organizations')->findOrFail($id);
        return $this->successResponse($user, 'Chi tiết người dùng');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'user_type_id' => 'nullable|exists:user_types,id',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $user->update($data);
        $user->load('type', 'organizations');

        return $this->successResponse($user, 'Cập nhật người dùng thành công');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->organizations()->detach();
        $user->delete();

        return $this->successMessage('Xóa người dùng thành công');
    }

    public function resetPassword(Request $request, $id)
    {
        $data = $request->validate([
            'new_password' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->password_hash = bcrypt($data['new_password']);
        $user->save();

        return $this->successMessage('Đặt lại mật khẩu thành công');
    }
}
