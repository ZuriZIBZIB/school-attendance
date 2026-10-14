@extends('layouts.app')
@section('title', $classroom->exists ? 'Edit Kelas' : 'Tambah Kelas')
@section('content')
    <h1 class="mb-4 text-xl font-semibold">{{ $classroom->exists ? 'Edit Kelas' : 'Tambah Kelas' }}</h1>

    <form method="POST"
        action="{{ $classroom->exists ? route('classrooms.update', $classroom) : route('classrooms.store') }}"
        class="max-w-lg space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if ($classroom->exists)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nama Kelas</label>
            <input id="name" name="name" value="{{ old('name', $classroom->name) }}" required maxlength="50"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="grade_level" class="block text-sm font-medium text-slate-700">Tingkat</label>
            <select id="grade_level" name="grade_level" required
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                @foreach ([10, 11, 12] as $grade)
                    <option value="{{ $grade }}" @selected(old('grade_level', $classroom->grade_level) == $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            @error('grade_level')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="teacher_id" class="block text-sm font-medium text-slate-700">Wali Kelas</label>
            <select id="teacher_id" name="teacher_id"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                <option value="">- Belum ditentukan -</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected(old('teacher_id', $classroom->teacher_id) == $teacher->id)>{{ $teacher->full_name }}</option>
                @endforeach
            </select>
            @error('teacher_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
            <a href="{{ route('classrooms.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection