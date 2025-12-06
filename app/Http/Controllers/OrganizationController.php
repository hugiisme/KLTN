<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrgJoinRequest;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponse;

class OrganizationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Organization::with('type', 'level', 'parent');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
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

        return $this->paginatedResponse($items, 'Danh sách tổ chức');
    }

    public function tree()
    {
        $organizations = Organization::with('children', 'type', 'level')
            ->whereNull('parent_org_id')
            ->get();

        $tree = $organizations->map(function ($org) {
            return $this->formatNode($org);
        });

        return $this->successResponse($tree, 'Cây tổ chức');
    }

    protected function formatNode($org)
    {
        return [
            'id' => $org->id,
            'name' => $org->name,
            'label' => $org->name,
            'description' => $org->description,
            'type' => 'organization',
            'org_type' => $org->type,
            'org_level' => $org->level,
            'parent_id' => $org->parent_org_id,
            'children' => $org->children->map(function ($child) {
                return $this->formatNode($child);
            }),
        ];
    }

    public function show($id)
    {
        $org = Organization::with('parent', 'type', 'level', 'children')->findOrFail($id);
        return $this->successResponse($org, 'Chi tiết tổ chức');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('organizations')->where(function ($query) use ($request) {
                    return $query->where('parent_org_id', $request->parent_org_id);
                }),
            ],
            'description'   => 'nullable|string',
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id'   => 'required|exists:org_types,id',
            'org_level_id'  => 'required|exists:org_levels,id',
        ]);

        $org = Organization::create($data);

        return $this->successResponse($org, 'Tạo tổ chức thành công', 201);
    }

    public function update(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('organizations')
                    ->ignore($org->id)
                    ->where(function ($query) use ($request) {
                        return $query->where('parent_org_id', $request->parent_org_id);
                    }),
            ],
            'description'   => 'nullable|string',
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id'   => 'required|exists:org_types,id',
            'org_level_id'  => 'required|exists:org_levels,id',
        ]);

        $org->update($data);

        return $this->successResponse($org, 'Cập nhật tổ chức thành công');
    }

    public function destroy($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();

        return $this->successMessage('Xóa tổ chức thành công');
    }

    public function getUsers(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $query = $org->users()->with('type');

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
        $users = $query->paginate($perPage);

        return $this->paginatedResponse($users, 'Danh sách người dùng trong tổ chức');
    }

    public function getPendingRequests(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $query = OrgJoinRequest::where('org_id', $id)
            ->where('status', 'pending')
            ->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sort_field')) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_direction ?? 'asc'
            );
        }

        $perPage = (int) ($request->per_page ?? 10);
        $requests = $query->paginate($perPage);

        return $this->paginatedResponse($requests, 'Danh sách yêu cầu chờ duyệt');
    }
}
