<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800">
@php
    $user = auth()->user();
    $menu = [
        ['Dashboard', route('dashboard'), 'dashboard*', ['admin', 'guru', 'siswa'], 'squares-2x2'],
        ['Kelas', route('classrooms.index'), 'classrooms*', ['admin'], 'academic-cap'],
        ['Siswa', route('students.index'), 'students*', ['admin'], 'users'],
        ['Absensi', route('attendances.index'), 'attendances*', ['admin', 'guru'], 'clipboard-document-check'],
    ];
@endphp
<div class="min-h-screen md:flex">
    <div id="sidebar-backdrop" class="fixed inset-0 z-20 hidden bg-slate-900/40 md:hidden"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white md:sticky md:top-0 md:flex md:h-screen">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <span class="text-lg font-semibold text-blue-700">{{ config('app.name') }}</span>
            <button type="button" id="sidebar-close" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 md:hidden" aria-label="Tutup menu">
                {{ svg('heroicon-o-x-mark', 'h-5 w-5') }}
            </button>
        </div>
        <nav class="flex-1 space-y-1 p-3">
            @foreach ($menu as [$label, $url, $pattern, $roles, $icon])
                @if (in_array($user->role->value, $roles, true))
                    <a href="{{ $url }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        {{ svg('heroicon-o-'.$icon, 'h-5 w-5') }}
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 md:px-6">
            <button type="button" id="sidebar-toggle" class="rounded-lg border border-slate-300 p-2 text-slate-600 hover:bg-slate-50 md:hidden" aria-label="Buka menu">
                {{ svg('heroicon-o-bars-3', 'h-5 w-5') }}
            </button>
            <div class="ml-auto flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-medium leading-tight">{{ $user->name }}</p>
                    <p class="text-xs capitalize text-slate-500">{{ $user->role->value }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white hover:bg-slate-700">
                        {{ svg('heroicon-o-arrow-right-on-rectangle', 'h-4 w-4') }}
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script>
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    const setSidebar = (open) => {
        sidebar.classList.toggle('hidden', !open);
        sidebar.classList.toggle('flex', open);
        backdrop.classList.toggle('hidden', !open);
    };

    document.getElementById('sidebar-toggle').addEventListener('click', () => setSidebar(true));
    document.getElementById('sidebar-close').addEventListener('click', () => setSidebar(false));
    backdrop.addEventListener('click', () => setSidebar(false));
    document.addEventListener('keydown', (event) => event.key === 'Escape' && setSidebar(false));
</script>
</body>
</html>