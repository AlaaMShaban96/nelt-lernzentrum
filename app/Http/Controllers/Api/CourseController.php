<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('registrations')->get();

        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|string',
            'start_date' => 'required|date',
            'seats' => 'required|integer|min:1',
        ]);

        $validated['available_seats'] = $validated['seats'];
        $course = Course::create($validated);

        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'level' => 'required|string',
            'start_date' => 'required|date',
            'seats' => 'required|integer|min:1',
        ]);

        $course->update($validated);

        return response()->json($course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(null, 204);
    }

    public function registrations($id)
    {
        $course = Course::with('registrations')->findOrFail($id);

        return response()->json($course->registrations);
    }
}
