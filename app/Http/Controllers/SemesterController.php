<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    // GET /api/manage/semesters
    public function index(Request $request)
    {
        logger('Query params:', $request->all());
        $query = Semester::query();

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        return response()->json($query->get());
    }

    // POST /api/manage/semesters
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
        ]);

        $semester = Semester::create($request->all());

        return response()->json($semester, 201);
    }

    // GET /api/manage/semesters/{id}
    public function show(Semester $semester)
    {
        return response()->json($semester);
    }

    // PUT /api/manage/semesters/{id}
    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'academic_year_id' => 'sometimes|required|integer|exists:academic_years,id',
        ]);

        // thêm academic_year_id vào list update
        $semester->update($request->only([
            'name',
            'start_date',
            'end_date',
            'academic_year_id'
        ]));

        return response()->json($semester);
    }


    // DELETE /api/manage/semesters/{id}
    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return response()->json(null, 204);
    }
}
