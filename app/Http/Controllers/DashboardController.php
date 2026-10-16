<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard.'.$request->user()->role->value);
    }

    public function admin(): View
    {
        $todayCounts = $this->statusCounts(Attendance::where('date', today()->toDateString()));

        return view('dashboard.admin', [
            'totalStudents' => Student::where('is_active', true)->count(),
            'totalTeachers' => Teacher::count(),
            'totalClassrooms' => Classroom::count(),
            'todayCounts' => $todayCounts,
            'todayPercentage' => $this->percentage($todayCounts),
            'chart' => $this->weeklyChart(),
            'recent' => Attendance::with(['student', 'classroom', 'recordedBy'])
                ->latest('updated_at')
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function guru(Request $request): View
    {
        $teacher = $request->user()->teacher;
        $today = today()->toDateString();

        $classrooms = $teacher
            ? $teacher->classrooms()
                ->withCount(['students as active_students_count' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get()
            : collect();

        $cards = $classrooms->map(function ($classroom) use ($today) {
            $counts = $this->statusCounts(
                Attendance::where('classroom_id', $classroom->id)->where('date', $today)
            );
            $recorded = array_sum($counts);

            return [
                'classroom' => $classroom,
                'counts' => $counts,
                'status' => match (true) {
                    $recorded === 0 => 'belum',
                    $recorded < $classroom->active_students_count => 'sebagian',
                    default => 'lengkap',
                },
            ];
        });

        return view('dashboard.guru', ['cards' => $cards]);
    }

    public function siswa(Request $request): View
    {
        // ID siswa selalu dari akun yang login, tidak pernah dari URL atau form
        $student = $request->user()->student?->load('classroom');

        if (! $student) {
            return view('dashboard.siswa', ['student' => null]);
        }

        $counts = $this->statusCounts(Attendance::where('student_id', $student->id));

        return view('dashboard.siswa', [
            'student' => $student,
            'counts' => $counts,
            'percentage' => $this->percentage($counts),
            'history' => Attendance::with('classroom')
                ->where('student_id', $student->id)
                ->orderByDesc('date')
                ->paginate(10),
        ]);
    }

    private function statusCounts(Builder $query): array
    {
        $counts = $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(AttendanceStatus::cases())
            ->mapWithKeys(fn ($status) => [$status->value => (int) ($counts[$status->value] ?? 0)])
            ->all();
    }

    private function percentage(array $counts): ?float
    {
        $total = array_sum($counts);

        return $total > 0 ? round($counts['hadir'] / $total * 100, 1) : null;
    }

    private function weeklyChart(): array
    {
        // 7 hari kerja terakhir, urut dari yang paling lama
        $days = collect(range(0, 11))
            ->map(fn ($i) => today()->subDays($i))
            ->reject(fn ($day) => $day->isWeekend())
            ->take(7)
            ->reverse()
            ->values();

        $rows = Attendance::selectRaw('date, status, count(*) as total')
            ->whereIn('date', $days->map(fn ($day) => $day->toDateString()))
            ->groupBy('date', 'status')
            ->get()
            ->groupBy(fn ($row) => $row->date->toDateString());

        return [
            'labels' => $days->map(fn ($day) => $day->translatedFormat('d M'))->all(),
            'datasets' => collect(AttendanceStatus::cases())->map(fn ($status) => [
                'label' => $status->label(),
                'color' => $status->color(),
                'data' => $days->map(fn ($day) => (int) (
                    $rows->get($day->toDateString())
                        ?->first(fn ($row) => $row->status === $status)
                        ?->total ?? 0
                ))->all(),
            ])->all(),
        ];
    }
}