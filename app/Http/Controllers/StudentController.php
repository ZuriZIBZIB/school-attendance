<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StudentRequest;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Student::class);

        $students = Student::with(['classroom', 'user'])
            ->when($request->string('q')->toString(), fn ($query, $search) => $query->where(
                fn ($w) => $w->where('full_name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%")
            ))
            ->when($request->integer('classroom'), fn ($query, $id) => $query->where('classroom_id', $id))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'aktif'))
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'classrooms' => $this->classrooms(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Student::class);

        return view('students.form', [
            'student' => new Student,
            'classrooms' => $this->classrooms(),
        ]);
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        Gate::authorize('create', Student::class);

        DB::transaction(function () use ($request) {
            $data = $this->studentData($request);
            $data['user_id'] = $this->saveAccount($request, null, $data['is_active'])?->id;
            Student::create($data);
        });

        return redirect()->route('students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student): View
    {
        Gate::authorize('update', $student);

        return view('students.form', [
            'student' => $student->load('user'),
            'classrooms' => $this->classrooms(),
        ]);
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        Gate::authorize('update', $student);

        DB::transaction(function () use ($request, $student) {
            $data = $this->studentData($request);
            $data['user_id'] = $this->saveAccount($request, $student->user, $data['is_active'])?->id;
            $student->update($data);
        });

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        Gate::authorize('delete', $student);

        // FK restrict: siswa dengan riwayat absensi tidak boleh dihapus
        if ($student->attendances()->exists()) {
            return back()->with('error', 'Siswa punya riwayat absensi dan tidak bisa dihapus. Nonaktifkan lewat Edit.');
        }

        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();
            $user?->delete();
        });

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    private function classrooms()
    {
        return Classroom::orderBy('grade_level')->orderBy('name')->get();
    }

    private function studentData(StudentRequest $request): array
    {
        return [
            ...$request->safe()->only(['nis', 'full_name', 'gender', 'classroom_id']),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    // Akun login siswa: role selalu 'siswa', status aktif mengikuti status siswa
    private function saveAccount(StudentRequest $request, ?User $user, bool $active): ?User
    {
        if ($user) {
            $user->fill([
                'name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'is_active' => $active,
            ]);

            if ($request->filled('password')) {
                $user->password = $request->input('password');
            }

            $user->save();

            return $user;
        }

        if ($request->filled('email')) {
            return User::create([
                'name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'role' => Role::Siswa,
                'is_active' => $active,
            ]);
        }

        return null;
    }
}