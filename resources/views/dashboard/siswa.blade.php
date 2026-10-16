@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <h1 class="mb-4 text-xl font-semibold">Dashboard Siswa</h1>

    @if (! $student)
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            Akun Anda belum terhubung dengan data siswa. Hubungi admin sekolah.
        </div>
    @else
        <p class="mb-4 text-sm text-slate-500">
            {{ $student->full_name }} &middot; NIS {{ $student->nis }} &middot; {{ $student->classroom->name }}
        </p>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
            <x-stat-card label="Kehadiran" :value="$percentage !== null ? $percentage.'%' : '-'" hint="Hadir ÷ total tercatat" />
            @foreach (App\Enums\AttendanceStatus::cases() as $status)
                <x-stat-card :label="$status->label()" :value="$counts[$status->value]" />
            @endforeach
        </div>

        <h2 class="mb-3 mt-8 text-sm font-semibold text-slate-700">Riwayat Absensi</h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($history as $row)
                        <tr>
                            <td class="px-4 py-3">{{ $row->date->translatedFormat('l, d F Y') }}</td>
                            <td class="px-4 py-3">{{ $row->classroom->name }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$row->status" /></td>
                            <td class="px-4 py-3 text-slate-500">{{ $row->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">Belum ada riwayat absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $history->links() }}</div>
    @endif
@endsection