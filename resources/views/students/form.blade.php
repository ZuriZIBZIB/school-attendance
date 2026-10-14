@extends('layouts.app')
@section('title', $student->exists ? 'Edit Siswa' : 'Tambah Siswa')
@section('content')
    <h1 class="mb-4 text-xl font-semibold">{{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa' }}</h1>

    <form method="POST"
        action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}"
        class="max-w-lg space-y-4 rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @if ($student->exists)
            @method('PUT')
        @endif

        <div>
            <label for="nis" class="block text-sm font-medium text-slate-700">NIS</label>
            <input id="nis" name="nis" value="{{ old('nis', $student->nis) }}" required maxlength="20"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
            @error('nis')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="full_name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <input id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required maxlength="100"
                class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
            @error('full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                <select id="gender" name="gender" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                    <option value="L" @selected(old('gender', $student->gender) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender', $student->gender) === 'P')>Perempuan</option>
                </select>
                @error('gender')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="classroom_id" class="block text-sm font-medium text-slate-700">Kelas</label>
                <select id="classroom_id" name="classroom_id" required
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                    @foreach ($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" @selected(old('classroom_id', $student->classroom_id) == $classroom->id)>{{ $classroom->name }}</option>
                    @endforeach
                </select>
                @error('classroom_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300"
                @checked(old('is_active', $student->exists ? $student->is_active : true))>
            Siswa aktif
        </label>

        <div class="space-y-4 border-t border-slate-200 pt-4">
            <p class="text-sm font-medium text-slate-700">Akun Login (opsional)</p>
            <div>
                <label for="email" class="block text-sm text-slate-600">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $student->user?->email) }}"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm text-slate-600">Password</label>
                <input id="password" type="password" name="password" autocomplete="new-password"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
                <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter. Saat edit, kosongkan jika tidak ingin mengganti.</p>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
            <a href="{{ route('students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection     