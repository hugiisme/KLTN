<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;
use App\Traits\ApiResponse;

class SemesterController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Semester::query();

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

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
        $items = $query->orderBy('start_date', 'desc')->paginate($perPage);

        return $this->paginatedResponse($items, 'Danh sách học kỳ');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
        ]);

        $semester = Semester::create($data);

        return $this->successResponse($semester, 'Tạo học kỳ thành công', 201);
    }

    public function show(Semester $semester)
    {
        return $this->successResponse($semester, 'Chi tiết học kỳ');
    }

    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
        ]);

        $semester->update($data);

        return $this->successResponse($semester, 'Cập nhật học kỳ thành công');
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return $this->successMessage('Xóa học kỳ thành công');
    }
}
