<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrgType;
use App\Models\OrgLevel;

class OrganizationController extends Controller
{
    /**
     * GET /api/manage/organizations
     */
    public function index(Request $request)
    {
        $query = Organization::with('type', 'level', 'parent');

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // FILTER: filter_field + filter_value
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $query->where($request->filter_field, $request->filter_value);
        }

        // SORTING
        if ($request->filled('sort_field')) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_direction ?? 'asc'
            );
        }

        // PAGINATION
        $perPage = (int) ($request->per_page ?? 10);
        $items = $query->paginate($perPage);

        return response()->json($items);
    }

    /**
     * TREE
     */
    public function tree()
    {
        $organizations = Organization::with('children', 'type', 'level')
            ->whereNull('parent_org_id')
            ->get();

        $tree = $organizations->map(function ($org) {
            return $this->formatNode($org);
        });

        return response()->json($tree);
    }

    protected function formatNode($org)
    {
        return [
            'id' => $org->id,
            'label' => $org->name,
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
        $org = Organization::with('parent', 'type', 'level', 'children')->find($id);

        if (!$org) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($org);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|unique:organizations,name',
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id'   => 'required|exists:org_types,id',
            'org_level_id'  => 'required|exists:org_levels,id',
        ]);

        $org = Organization::create($data);

        return response()->json([
            'message' => 'Tạo tổ chức mới thành công',
            'type' => 'success',
            'data' => $org
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:organizations,name,' . $org->id,
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id'   => 'required|exists:org_types,id',
            'org_level_id'  => 'required|exists:org_levels,id',
        ]);

        $org->update($data);

        return response()->json($org);
    }

    public function destroy($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();

        return response()->json([
            'message' => 'Xóa tổ chức thành công',
            'type' => 'success',
        ]);
    }

    public function getTypes()
    {
        return response()->json(OrgType::all());
    }

    public function getLevels()
    {
        return response()->json(OrgLevel::all());
    }
    public function getUsers(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        // SEARCH
        $query = $org->users()->with('type');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
                // ->orWhere('email', 'like', "%{$search}%")
                // ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // PAGINATION
        $perPage = (int) ($request->per_page ?? 10);
        $users = $query->paginate($perPage);

        return response()->json($users);
    }
}
