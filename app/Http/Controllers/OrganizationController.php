<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrgType;
use App\Models\OrgLevel;

class OrganizationController extends Controller
{
    /**
     * Trả về cây tổ chức
     */
    public function tree()
    {
        // Lấy các org cha (parent_org_id null) và eager load children
        $organizations = Organization::with('children', 'type', 'level')->whereNull('parent_org_id')->get();

        $tree = $organizations->map(function ($org) {
            return $this->formatNode($org);
        });

        return response()->json($tree);
    }

    /**
     * Format node cho tree
     */
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

    /**
     * Hiển thị chi tiết tổ chức
     */
    public function show($id)
    {
        $org = Organization::with('parent', 'type', 'level', 'children')->find($id);

        if (!$org) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($org);
    }

    /**
     * Tạo tổ chức mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:organizations,name',
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id' => 'required|exists:org_types,id',
            'org_level_id' => 'required|exists:org_levels,id',
        ]);

        $org = Organization::create($data);

        return response()->json([
            'message' => 'Tạo tổ chức mới thành công',
            'type' => 'success',
            'data' => $org
        ], 201);
    }

    /**
     * Cập nhật tổ chức
     */
    public function update(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $org->id,
            'parent_org_id' => 'nullable|exists:organizations,id',
            'org_type_id' => 'required|exists:org_types,id',
            'org_level_id' => 'required|exists:org_levels,id',
        ]);

        $org->update($data);

        return response()->json($org);
    }

    /**
     * Xóa tổ chức
     */
    public function destroy($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();

        return response()->json([
            'message' => 'Xóa tổ chức thành công',
            'type' => 'success',
        ]);
    }

    /**
     * Lấy danh sách loại tổ chức cho dropdown
     */
    public function getTypes()
    {
        return response()->json(OrgType::all());
    }

    /**
     * Lấy danh sách cấp tổ chức cho dropdown
     */
    public function getLevels()
    {
        return response()->json(OrgLevel::all());
    }
}
