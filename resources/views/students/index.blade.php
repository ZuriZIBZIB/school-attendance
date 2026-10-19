@extends('layouts.app')
@section('title', 'Siswa')
@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Siswa</h1>
                <a href="{{ route('students.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            {{ svg('heroicon-o-plus', 'h-4 w-4') }} Tambah Siswa
        </a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS..."
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600 sm:w-64">
        <select name="classroom" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">Semua kelas</option>
            @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}" @selected(request('classroom') == $classroom->id)>{{ $classroom->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>
        <button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">NIS</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">JK</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Akun</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr>
                        <td class="px-4 py-3">{{ $student->nis }}</td>
                        <td class="px-4 py-3 font-medium">{{ $student->full_name }}</td>
                        <td class="px-4 py-3">{{ $student->gender }}</td>
                        <td class="px-4 py-3">{{ $student->classroom->name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $student->user?->email ?? '-' }}</td>
                        <td class="px-4 py-3">
                                                        <div class="flex justify-end gap-2">
                                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1 hover:bg-slate-50">
                                    {{ svg('heroicon-o-pencil-square', 'h-4 w-4') }} Edit
                                </a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Hapus siswa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1 text-red-600 hover:bg-red-50">
                                        {{ svg('heroicon-o-trash', 'h-4 w-4') }} Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">Tidak ada siswa yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $students->links() }}</div>
@endsection