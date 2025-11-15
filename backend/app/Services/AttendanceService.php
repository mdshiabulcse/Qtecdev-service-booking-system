<?php
namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function recordBulkAttendance(array $attendanceData, int $recordedBy): array
    {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::transaction(function () use ($attendanceData, $recordedBy, &$results) {
            foreach ($attendanceData as $data) {
                try {
                    $attendance = Attendance::updateOrCreate(
                        [
                            'student_id' => $data['student_id'],
                            'date' => $data['date']
                        ],
                        [
                            'status' => $data['status'],
                            'note' => $data['note'] ?? null,
                            'recorded_by' => $recordedBy
                        ]
                    );

                    event(new AttendanceRecorded($attendance));
                    $results['successful']++;

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Student ID {$data['student_id']}: {$e->getMessage()}";
                }
            }
        });

        $this->clearAttendanceCache();
        return $results;
    }

    public function getMonthlyReport(string $month, ?string $class = null): array
    {
        $cacheKey = "attendance_report_{$month}_" . ($class ?? 'all');

        return Cache::remember($cacheKey, 3600, function () use ($month, $class) {
            $query = Student::with(['attendances' => function ($query) use ($month) {
                $query->whereMonth('date', date('m', strtotime($month)))
                    ->whereYear('date', date('Y', strtotime($month)));
            }]);

            if ($class) {
                $query->where('class', $class);
            }

            return $query->active()->get()->map(function ($student) use ($month) {
                $year = date('Y', strtotime($month));
                $monthNum = date('m', strtotime($month));

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
        });
    }

    public function getTodaySummary(): array
    {
        return Cache::remember('attendance_today_summary', 300, function () {
            $today = now()->format('Y-m-d');

            $totalStudents = Student::active()->count();
            $todayAttendance = Attendance::where('date', $today)->get();

            $present = $todayAttendance->where('status', 'present')->count();
            $absent = $todayAttendance->where('status', 'absent')->count();
            $late = $todayAttendance->where('status', 'late')->count();
            $halfDay = $todayAttendance->where('status', 'half_day')->count();
            $totalMarked = $present + $absent + $late + $halfDay;

            return [
                'total_students' => $totalStudents,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'not_marked' => $totalStudents - $totalMarked,
                'attendance_rate' => $totalStudents > 0 ?
                    round((($present + $late + ($halfDay * 0.5)) / $totalStudents) * 100, 2) : 0
            ];
        });
    }

    private function clearAttendanceCache(): void
    {
        Cache::forget('attendance_today_summary');
        Cache::forget('dashboard_stats');
        Cache::forget('monthly_attendance_chart');
        Cache::forget('recent_activities');
    }
}
