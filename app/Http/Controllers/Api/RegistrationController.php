<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $registration = Registration::create($validated);

        return response()->json($registration, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:registered,confirmed,canceled',
        ]);

        $registration->status = $validated['status'];

        $registration->save();
        if ($validated['status'] == 'confirmed') {
            $registration->course->available_seats--;
            $registration->course->save();
        }

        return response()->json($registration);
    }
}
