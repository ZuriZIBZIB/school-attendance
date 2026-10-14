@extends('layouts.app')
@section('title', 'Absensi')
@section('content')
    @php
        $chip = [
            'hadir' => 'peer-checked:bg-green-600',
            'izin' => 'peer-checked:bg-blue-600',
            'sakit' => 'peer-checked:bg-amber-500',
            'alpa' => 'peer-checked:bg-red-600',
        ];
    @endphp

    <h1 class="mb-4 text-xl font-semibold">Absensi Harian</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('attendances.index') }}" class="mb-4 flex flex-wrap items-end gap-2">
        <div>
            <label for="classroom" class="block text-xs font-medium text-slate-600">Kelas</label>
            <select id="classroom" name="classroom" required class="mt-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                <option value="">- Pilih kelas -</option>
                @foreach ($classrooms as $item)
                    <option value="{{ $item->id }}" @selected($classroom?->id === $item->id)>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="date" class="block text-xs font-medium text-slate-600">Tanggal</label>
            <input id="date" type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}"
                class="mt-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
        </div>
        <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tampilkan</button>
    </form>

    @if ($classrooms->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            Belum ada kelas yang menjadi tanggung jawab Anda.
        </div>
    @elseif (! $classroom)
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            Pilih kelas dan tanggal untuk mulai mencatat absensi.
        </div>
    @elseif ($students->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
            Kelas {{ $classroom->name }} belum memiliki siswa aktif.
        </div>
    @else
        @unless ($editable)
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Tanggal ini hanya dapat dilihat. Guru hanya bisa mengubah absensi hari ini sampai 7 hari ke belakang.
            </div>
        @endunless

        <form method="POST" action="{{ route('attendances.store') }}">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
            <input type="hidden" name="date" value="{{ $date->toDateString() }}">

            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <p class="text-sm text-slate-600">
                    {{ $classroom->name }} &middot; {{ $date->translatedFormat('l, d F Y') }} &middot; {{ $students->count() }} siswa
                </p>
                @if ($editable)
                    <button type="button" id="mark-all-present" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
                        Tandai semua hadir
                    </button>
                @endif
            </div>

            <fieldset @disabled(! $editable)>
                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">NIS</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($students as $i => $student)
                                @php
                                    $record = $existing->get($student->id);
                                    $current = old("records.$i.status", $record?->status->value ?? 'hadir');
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">{{ $student->nis }}</td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ $student->full_name }}
                                        <input type="hidden" name="records[{{ $i }}][student_id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach (App\Enums\AttendanceStatus::cases() as $status)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="records[{{ $i }}][status]" value="{{ $status->value }}"
                                                        class="peer sr-only" @checked($current === $status->value)>
                                                    <span class="block rounded-lg border border-slate-300 px-3 py-1 text-xs font-medium text-slate-600 peer-checked:border-transparent peer-checked:text-white {{ $chip[$status->value] }}">
                                                        {{ $status->label() }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="records[{{ $i }}][note]" maxlength="255"
                                            value="{{ old("records.$i.note", $record?->note) }}" placeholder="Opsional"
                                            class="w-full min-w-40 rounded-lg border border-slate-300 px-3 py-1.5 text-sm outline-none focus:border-blue-600">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>

            @if ($editable)
                <div class="mt-4">
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Absensi</button>
                </div>
            @endif
        </form>

        <script>
            document.getElementById('mark-all-present')?.addEventListener('click', () => {
                document.querySelectorAll('input[type=radio][value=hadir]').forEach((radio) => radio.checked = true);
            });
        </script>
    @endif
@endsection