@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    @php
        $pill = [
            'belum' => ['Belum diisi', 'bg-red-100 text-red-700'],
            'sebagian' => ['Sebagian terisi', 'bg-amber-100 text-amber-700'],
            'lengkap' => ['Sudah diisi', 'bg-green-100 text-green-700'],
        ];
    @endphp

    <h1 class="text-xl font-semibold">Dashboard Guru</h1>
    <p class="mb-4 text-sm text-slate-500">{{ today()->translatedFormat('l, d F Y') }}</p>

    @forelse ($cards as $card)
        @php $classroom = $card['classroom']; @endphp
        <div class="mb-4 rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold">{{ $classroom->name }}</h2>
                    <p class="text-sm text-slate-500">Tingkat {{ $classroom->grade_level }} &middot; {{ $classroom->active_students_count }} siswa aktif</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $pill[$card['status']][1] }}">{{ $pill[$card['status']][0] }}</span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach (App\Enums\AttendanceStatus::cases() as $status)
                    <div class="rounded-lg bg-slate-50 p-3">
                        <x-status-badge :status="$status" />
                        <p class="mt-2 text-xl font-semibold">{{ $card['counts'][$status->value] }}</p>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('attendances.index', ['classroom' => $classroom->id, 'date' => today()->toDateString()]) }}"
                class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                {{ $card['status'] === 'lengkap' ? 'Lihat / Edit Absensi' : 'Catat Absensi' }}
            </a>
        </div>
    @empty
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            Belum ada kelas yang menjadi tanggung jawab Anda.
        </div>
    @endforelse
@endsection