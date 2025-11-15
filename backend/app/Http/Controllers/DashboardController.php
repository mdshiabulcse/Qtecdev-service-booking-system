<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(): JsonResponse
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            $totalStudents = Student::active()->count();
            $totalTeachers = User::where('role', 'teacher')->count();
            $today = now()->format('Y-m-d');

            $todayAttendance = Attendance::where('date', $today)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $present = $todayAttendance['present'] ?? 0;
            $absent = $todayAttendance['absent'] ?? 0;
            $late = $todayAttendance['late'] ?? 0;
            $halfDay = $todayAttendance['half_day'] ?? 0;
            $totalMarked = $present + $absent + $late + $halfDay;

            $monthlyStats = $this->getMonthlyStats(now()->format('Y-m'));

            return [
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'present_today' => $present,
                'absent_today' => $absent,
                'late_today' => $late,
                'half_day_today' => $halfDay,
                'not_marked_today' => $totalStudents - $totalMarked,
                'attendance_rate_today' => $totalStudents > 0 ?
                    round((($present + $late + ($halfDay * 0.5)) / $totalStudents) * 100, 2) : 0,
                'monthly_attendance_rate' => $monthlyStats['attendance_rate'],
                'class_wise_stats' => $this->getClassWiseStats(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getRecentActivities(): JsonResponse
    {
        $activities = Cache::remember('recent_activities', 300, function () {
            return Attendance::with(['student', 'recordedBy'])
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($attendance) {
                    return [
                        'id' => $attendance->id,
                        'student_name' => $attendance->student->name,
                        'student_class' => $attendance->student->class,
                        'status' => $attendance->status,
                        'status_color' => $attendance->status_color,
                        'date' => $attendance->date->format('M j, Y'),
                        'recorded_by' => $attendance->recordedBy->name,
                        'time' => $attendance->created_at->format('h:i A')
                    ];
                })
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function getMonthlyChart(): JsonResponse
    {
        $chartData = Cache::remember('monthly_attendance_chart', 3600, function () {
            $startDate = now()->subDays(30)->startOfDay();
            $endDate = now()->endOfDay();

            return Attendance::whereBetween('date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                    DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                    DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'present' => $item->present,
                        'absent' => $item->absent,
                        'late' => $item->late,
                        'total' => $item->total
                    ];
                })
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    public function getClassPerformance(): JsonResponse
    {
        $stats = Cache::remember('class_performance', 3600, function () {
            $classes = Student::active()
                ->select('class', DB::raw('COUNT(*) as student_count'))
                ->groupBy('class')
                ->orderBy('class')
                ->get();

            $classStats = [];
            foreach ($classes as $class) {
                $attendanceRate = $this->getClassAttendanceRate($class->class);
                $classStats[] = [
                    'class' => $class->class,
                    'student_count' => $class->student_count,
                    'attendance_rate' => $attendanceRate,
                    'status' => $this->getPerformanceStatus($attendanceRate)
                ];
            }

            return $classStats;
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    private function getMonthlyStats(string $month): array
    {
        $year = date('Y', strtotime($month));
        $monthNum = date('m', strtotime($month));

        $stats = Attendance::whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->select(
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day')
            )
            ->first();

        $totalExpected = Student::active()->count() * now()->daysInMonth;
        $attendanceRate = $totalExpected > 0 ?
            round((($stats->present + $stats->late + ($stats->half_day * 0.5)) / $totalExpected) * 100, 2) : 0;

        return [
            'total_attendance' => $stats->total_records,
            'attendance_rate' => $attendanceRate,
        ];
    }

    private function getClassWiseStats(): array
    {
        $classes = Student::active()
            ->select('class', DB::raw('COUNT(*) as student_count'))
            ->groupBy('class')
            ->orderBy('class')
            ->get();

        $classStats = [];
        foreach ($classes as $class) {
            $attendanceRate = $this->getClassAttendanceRate($class->class);
            $classStats[] = [
                'class' => $class->class,
                'student_count' => $class->student_count,
                'attendance_rate' => $attendanceRate,
            ];
        }

        return $classStats;
    }

    private function getClassAttendanceRate(string $class): float
    {
        $today = now()->format('Y-m-d');
        $totalStudents = Student::active()->where('class', $class)->count();

        if ($totalStudents === 0) return 0;

        $presentCount = Attendance::whereHas('student', function ($query) use ($class) {
            $query->where('class', $class)->active();
        })
            ->whereDate('date', $today)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($presentCount / $totalStudents) * 100, 2);
    }

    private function getPerformanceStatus(float $rate): string
    {
        if ($rate >= 90) return 'excellent';
        if ($rate >= 75) return 'good';
        if ($rate >= 60) return 'average';
        return 'poor';
    }
}
