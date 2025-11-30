<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;

class AcademicYearController extends Controller
{
    public function tree()
    {
        $years = AcademicYear::with('semesters')->orderBy('start_date')->get();

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

        return response()->json($tree);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:academic_years,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $academicYear = AcademicYear::create($data);

        return response()->json([
            'message' => 'Tạo năm học mới thành công',
            'type' => "success",
            'data' => $academicYear
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $year->update($request->only(['name', 'start_date', 'end_date']));

        return response()->json($year);
    }
}
