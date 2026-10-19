@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <h1 class="mb-4 text-xl font-semibold">Dashboard Admin</h1>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Siswa Aktif" :value="$totalStudents" />
        <x-stat-card label="Guru" :value="$totalTeachers" />
        <x-stat-card label="Kelas" :value="$totalClassrooms" />
    </div>

    <h2 class="mb-3 mt-8 text-sm font-semibold text-slate-700">Absensi Hari Ini &middot; {{ today()->translatedFormat('l, d F Y') }}</h2>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @foreach (App\Enums\AttendanceStatus::cases() as $status)
            <x-stat-card :label="$status->label()" :value="$todayCounts[$status->value]" />
        @endforeach
        <x-stat-card label="Kehadiran" :value="$todayPercentage !== null ? $todayPercentage.'%' : '-'" hint="Hadir ÷ total tercatat" />
    </div>
    @if (array_sum($todayCounts) === 0)
        <p class="mt-2 text-xs text-slate-500">Belum ada absensi yang dicatat hari ini.</p>
    @endif

    <div class="mt-8 rounded-xl border border-slate-200 bg-white p-4">
        <h2 class="mb-3 text-sm font-semibold text-slate-700">Rekap Kehadiran 7 Hari Kerja Terakhir</h2>
        <div class="relative h-72">
                        <p id="chart-loading" class="absolute inset-0 flex items-center justify-center text-sm text-slate-400">Memuat grafik...</p>
            <canvas id="attendance-chart" data-chart="{{ json_encode($chart) }}"></canvas>
        </div>
    </div>

    <h2 class="mb-3 mt-8 text-sm font-semibold text-slate-700">Absensi Terbaru</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Dicatat oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($recent as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $row->date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $row->student->full_name }}</td>
                        <td class="px-4 py-3">{{ $row->classroom->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$row->status" /></td>
                        <td class="px-4 py-3 text-slate-500">{{ $row->recordedBy->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection