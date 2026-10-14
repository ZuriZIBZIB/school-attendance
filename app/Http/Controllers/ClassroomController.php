<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassroomRequest;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Classroom::class);

        $classrooms = Classroom::with('teacher')
            ->withCount('students')
            ->when($request->string('q')->toString(), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('classrooms.index', compact('classrooms'));
    }

    public function create(): View
    {
        Gate::authorize('create', Classroom::class);

        return view('classrooms.form', [
            'classroom' => new Classroom,
            'teachers' => Teacher::orderBy('full_name')->get(),
        ]);
    }

    public function store(ClassroomRequest $request): RedirectResponse
    {
        Gate::authorize('create', Classroom::class);

        Classroom::create($request->validated());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom): View
    {
        Gate::authorize('update', $classroom);

        return view('classrooms.form', [
            'classroom' => $classroom,
            'teachers' => Teacher::orderBy('full_name')->get(),
        ]);
    }

    public function update(ClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        Gate::authorize('update', $classroom);

        $classroom->update($request->validated());

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        Gate::authorize('delete', $classroom);

        // FK restrict: kelas dengan siswa atau riwayat absensi tidak boleh dihapus
        if ($classroom->students()->exists() || $classroom->attendances()->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa atau riwayat absensi, tidak bisa dihapus.');
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus.');
    }
}