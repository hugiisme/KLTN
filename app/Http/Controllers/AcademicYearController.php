<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Traits\ApiResponse;

class AcademicYearController extends Controller
{
    use ApiResponse;


    public function index(Request $request)
    {
        $query = AcademicYear::query();

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

        return $this->paginatedResponse($items, 'Danh sách năm học');
    }

    public function tree()
    {
        $years = AcademicYear::with('semesters')->orderBy('start_date', 'desc')->get();

        $tree = $years->map(function ($year) {
            return [
                'id' => $year->id,
                'label' => $year->name,
                'start_date' => $year->start_date,
                'end_date' => $year->end_date,
                'type' => 'academic_year',
                "open" => false,
                'children' => $year->semesters->map(function ($semester) {
                    return [
                        'id' => $semester->id,
                        'label' => $semester->name,
                        'start_date' => $semester->start_date,
                        'end_date' => $semester->end_date,
                        'type' => 'semester',
                        'children' => [],
                    ];
                }),
            ];
        });

        return $this->successResponse($tree, 'Cây năm học - học kỳ');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $academicYear = AcademicYear::create($data);

        return $this->successResponse($academicYear, 'Tạo năm học thành công', 201);
    }

    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|unique:academic_years,name,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $year->update($data);

        return $this->successResponse($year, 'Cập nhật năm học thành công');
    }

    public function destroy($id)
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return $this->successMessage('Xóa năm học thành công');
    }
}
