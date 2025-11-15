<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Student::query();

        if ($request->has('class')) {
            $query->where('class', $request->class);
        }

        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('student_id', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $students = $query->orderBy('class')
            ->orderBy('section')
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return \App\Http\Resources\StudentResource::collection($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|unique:students',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students',
            'class' => 'required|string',
            'section' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|in:male,female,other',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully',
            'data' => new \App\Http\Resources\StudentResource($student)
        ], 201);
    }

    public function show(Student $student): \App\Http\Resources\StudentResource
    {
        return new \App\Http\Resources\StudentResource($student);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|nullable|email|unique:students,email,' . $student->id,
            'class' => 'sometimes|required|string',
            'section' => 'sometimes|required|string',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|required|in:male,female,other',
            'parent_name' => 'sometimes|required|string|max:255',
            'parent_phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|nullable|string',
            'photo' => 'sometimes|nullable|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => new \App\Http\Resources\StudentResource($student)
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully'
        ]);
    }

    public function getAttendance(Student $student, Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'sometimes|date_format:Y-m',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date'
        ]);

        $query = $student->attendances()->with('recordedBy');

        if ($request->has('month')) {
            $query->whereYear('date', date('Y', strtotime($request->month)))
                ->whereMonth('date', date('m', strtotime($request->month)));
        }

        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(30);

        return response()->json([
            'data' => $attendances
        ]);
    }

    public function getByClassSection($class, $section): AnonymousResourceCollection
    {
        $students = Student::where('class', $class)
            ->where('section', $section)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return \App\Http\Resources\StudentResource::collection($students);
    }
}
