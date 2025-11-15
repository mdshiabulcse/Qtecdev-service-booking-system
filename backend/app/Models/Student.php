<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'class',
        'section',
        'date_of_birth',
        'gender',
        'parent_name',
        'parent_phone',
        'address',
        'photo',
        'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceSummaries()
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function getMonthlyAttendancePercentage($month, $year): float
    {
        $totalDays = $this->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->count();

        $presentDays = $this->attendances()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'present')
            ->count();

        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
    }

    public function getTodayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByClassSection($query, $class, $section)
    {
        return $query->where('class', $class)->where('section', $section);
    }
}
