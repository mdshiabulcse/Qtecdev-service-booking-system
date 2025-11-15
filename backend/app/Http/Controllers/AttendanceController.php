<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function recordBulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,half_day',
            'attendances.*.note' => 'nullable|string'
        ]);

        $results = [
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::transaction(function () use ($validated, &$results) {
            foreach ($validated['attendances'] as $data) {
                try {
                    $attendance = Attendance::updateOrCreate(
                        [
                            'student_id' => $data['student_id'],
                            'date' => $validated['date']
                        ],
                        [
                            'status' => $data['status'],
                            'note' => $data['note'] ?? null,
                            'recorded_by' => auth()->id()
                        ]
                    );

                    $results['successful']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Student ID {$data['student_id']}: {$e->getMessage()}";
                }
            }
        });

        // Clear relevant caches
        \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
        \Illuminate\Support\Facades\Cache::forget('recent_activities');

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'results' => $results
        ]);
    }

    public function getTodayAttendance(): JsonResponse
    {
        $today = now()->format('Y-m-d');

        $totalStudents = Student::active()->count();
        $todayAttendance = Attendance::where('date', $today)->get();

        $present = $todayAttendance->where('status', 'present')->count();
        $absent = $todayAttendance->where('status', 'absent')->count();
        $late = $todayAttendance->where('status', 'late')->count();
        $halfDay = $todayAttendance->where('status', 'half_day')->count();
        $totalMarked = $present + $absent + $late + $halfDay;

        return response()->json([
            'data' => [
                'total_students' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'not_marked' => $totalStudents - $totalMarked,
                'attendance_rate' => $totalStudents > 0 ?
                    round((($present + $late + ($halfDay * 0.5)) / $totalStudents) * 100, 2) : 0
            ]
        ]);
    }

    public function getMonthlyReport(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'class' => 'nullable|string'
        ]);

        $query = Student::with(['attendances' => function ($query) use ($request) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                ->whereYear('date', date('Y', strtotime($request->month)));
        }]);

        if ($request->class) {
            $query->where('class', $request->class);
        }

        $report = $query->active()->get()->map(function ($student) use ($request) {
            $year = date('Y', strtotime($request->month));
            $monthNum = date('m', strtotime($request->month));

            return [
                'student_id' => $student->student_id,
                'name' => $student->name,
                'class' => $student->class,
                'section' => $student->section,
                'attendance_percentage' => $student->getMonthlyAttendancePercentage($monthNum, $year),
                'total_days' => $student->attendances->count(),
                'present_days' => $student->attendances->where('status', 'present')->count(),
                'absent_days' => $student->attendances->where('status', 'absent')->count(),
                'late_days' => $student->attendances->where('status', 'late')->count(),
                'half_days' => $student->attendances->where('status', 'half_day')->count(),
            ];
        })->toArray();

        return response()->json([
            'data' => $report
        ]);
    }

    public function getClassAttendance($class, $section, Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'sometimes|date'
        ]);

        $date = $request->date ?? now()->format('Y-m-d');

        $students = Student::where('class', $class)
            ->where('section', $section)
            ->with(['attendances' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }])
            ->get();

        return response()->json([
            'data' => $students
        ]);
    }

    public function getStudentAttendance($studentId, Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'sometimes|date_format:Y-m',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date'
        ]);

        $query = Attendance::where('student_id', $studentId)
            ->with('recordedBy');

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
}
