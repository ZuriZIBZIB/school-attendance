<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'classroom' => ['nullable', 'integer'],
            'date' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $classrooms = $this->accessibleClassrooms($user);
        $date = $request->filled('date') ? Carbon::parse($request->input('date'))->startOfDay() : today();

        $classroom = $classrooms->firstWhere('id', $request->integer('classroom'));

        // Kelas di luar kewenangan (mis. URL diubah manual) ditolak
        if ($request->filled('classroom')) {
            abort_unless($classroom, 403);
        }

        $students = collect();
        $existing = collect();

        if ($classroom) {
            $students = $classroom->students()->where('is_active', true)->orderBy('full_name')->get();
            $existing = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendances.index', [
            'classrooms' => $classrooms,
            'classroom' => $classroom,
            'date' => $date,
            'students' => $students,
            'existing' => $existing,
            'editable' => $classroom && $user->canRecordAttendanceOn($date),
        ]);
    }

    public function store(AttendanceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $classroom = Classroom::findOrFail($request->integer('classroom_id'));

        Gate::authorize('recordAttendance', $classroom);

        $date = Carbon::parse($request->input('date'))->startOfDay();

        if (! $user->canRecordAttendanceOn($date)) {
            throw ValidationException::withMessages([
                'date' => 'Tanggal tidak diizinkan. Guru hanya bisa mencatat hari ini sampai 7 hari ke belakang, dan tidak boleh tanggal mendatang.',
            ]);
        }

        $records = collect($request->input('records'));

        // Setiap siswa aktif di kelas wajib terisi, dan tidak boleh ada siswa dari kelas lain
        $activeIds = $classroom->students()->where('is_active', true)->pluck('id')
            ->map(fn ($id) => (int) $id)->sort()->values()->all();
        $submittedIds = $records->pluck('student_id')
            ->map(fn ($id) => (int) $id)->sort()->values()->all();

        if ($activeIds !== $submittedIds) {
            throw ValidationException::withMessages([
                'records' => 'Daftar siswa tidak sesuai dengan data kelas. Muat ulang halaman lalu coba lagi.',
            ]);
        }

        // Transaksi + updateOrCreate: simpan massal atomik, satu catatan per siswa per tanggal
        DB::transaction(function () use ($records, $classroom, $date, $user) {
            foreach ($records as $row) {
                Attendance::updateOrCreate(
                    ['student_id' => (int) $row['student_id'], 'date' => $date->toDateString()],
                    [
                        'classroom_id' => $classroom->id,
                        'status' => $row['status'],
                        'note' => $row['note'] ?? null,
                        'recorded_by' => $user->id,
                    ]
                );
            }
        });

        return redirect()
            ->route('attendances.index', ['classroom' => $classroom->id, 'date' => $date->toDateString()])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    private function accessibleClassrooms(User $user): Collection
    {
        if ($user->isAdmin()) {
            return Classroom::orderBy('grade_level')->orderBy('name')->get();
        }

        if (! $user->teacher) {
            return collect();
        }

        return $user->teacher->classrooms()->orderBy('grade_level')->orderBy('name')->get();
    }
}