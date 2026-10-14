@extends('layouts.app')
@section('title', 'Kelas')
@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Kelas</h1>
        <a href="{{ route('classrooms.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tambah Kelas</a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama kelas..."
            class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-600">
        <button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50">Cari</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama Kelas</th>
                    <th class="px-4 py-3">Tingkat</th>
                    <th class="px-4 py-3">Wali Kelas</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($classrooms as $classroom)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $classroom->name }}</td>
                        <td class="px-4 py-3">{{ $classroom->grade_level }}</td>
                        <td class="px-4 py-3">{{ $classroom->teacher?->full_name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $classroom->students_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('classrooms.edit', $classroom) }}" class="rounded-lg border border-slate-300 px-3 py-1 hover:bg-slate-50">Edit</a>
                                <form method="POST" action="{{ route('classrooms.destroy', $classroom) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-200 px-3 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            Belum ada kelas{{ request('q') ? ' yang cocok dengan pencarian' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $classrooms->links() }}</div>
@endsection 