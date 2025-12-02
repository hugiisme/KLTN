<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    /**
     * GET /api/manage/semesters
     */
    public function index(Request $request)
    {
        $query = Semester::query();

        // FILTER: academic_year_id
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // SEARCH: search on name (customize as needed)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // CUSTOM FILTER: filter_field + filter_value
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

    public function show(Semester $semester)
    {
        return response()->json($semester);
    }

    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'start_date'    => 'sometimes|required|date',
            'end_date'      => 'sometimes|required|date',
            'academic_year_id' => 'sometimes|required|integer|exists:academic_years,id',
        ]);

        $semester->update($request->only([
            'name',
            'start_date',
            'end_date',
            'academic_year_id'
        ]));

        return response()->json($semester);
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return response()->json(null, 204);
    }
}
