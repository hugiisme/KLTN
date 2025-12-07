<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\ActivityCategory;
use App\Traits\ApiResponse;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Activity::with([
            'activityType',
            'activityCategory',
            'creator',
            'organization',
            'semester',
            'academicYear',
            'submissionRequirement'
        ]);

        if ($request->filled('org_id')) {
            $query->where('org_id', $request->org_id);
        } else {
            return $this->paginatedResponse(Activity::whereNull('id')->paginate(10), 'Vui lòng chọn tổ chức');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $query->where($request->filter_field, $request->filter_value);
        }

        if ($request->filled('sort_field')) {
            $query->orderBy(
                $request->sort_field,
                $request->sort_direction ?? 'asc'
            );
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) ($request->per_page ?? 10);
        $items = $query->paginate($perPage);

        return $this->paginatedResponse($items, 'Danh sách hoạt động');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'org_id' => 'required|exists:organizations,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'parent_activity_id' => 'nullable|exists:activities,id',
            'submission_requirement_id' => 'nullable|exists:submission_requirements,id',
            'is_visible' => 'boolean',
            'activity_type_id' => 'required|exists:activity_types,id',
            'activity_category_id' => 'required|exists:activity_categories,id',
            'status' => ['required', Rule::in(['draft', 'verified', 'archived'])],
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        $data['creator_id'] = $request->user() ? $request->user()->id : 1;
        $activity = Activity::create($data);

        return $this->successResponse($activity->load([
            'activityType',
            'activityCategory',
            'creator',
            'organization'
        ]), 'Tạo hoạt động thành công', 201);
    }

    public function show($id)
    {
        $activity = Activity::with([
            'activityType',
            'activityCategory',
            'creator',
            'organization',
            'semester',
            'academicYear',
            'submissionRequirement',
            'parent',
            'children'
        ])->findOrFail($id);

        return $this->successResponse($activity, 'Chi tiết hoạt động');
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'org_id' => 'required|exists:organizations,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'parent_activity_id' => 'nullable|exists:activities,id',
            'submission_requirement_id' => 'nullable|exists:submission_requirements,id',
            'is_visible' => 'boolean',
            'activity_type_id' => 'required|exists:activity_types,id',
            'activity_category_id' => 'required|exists:activity_categories,id',
            'status' => ['required', Rule::in(['draft', 'verified', 'archived'])],
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        $activity->update($data);

        return $this->successResponse($activity->load([
            'activityType',
            'activityCategory',
            'creator',
            'organization'
        ]), 'Cập nhật hoạt động thành công');
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return $this->successMessage('Xóa hoạt động thành công');
    }

    public function getActivityTypes()
    {
        $types = ActivityType::all();
        return $this->successResponse($types, 'Danh sách loại hoạt động');
    }

    public function getActivityCategories()
    {
        $categories = ActivityCategory::all();
        return $this->successResponse($categories, 'Danh sách phân loại hoạt động');
    }
}
