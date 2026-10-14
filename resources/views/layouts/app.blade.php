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
        ['Dashboard', route('dashboard'), 'dashboard*', ['admin', 'guru', 'siswa']],
        ['Kelas', route('classrooms.index'), 'classrooms*', ['admin']],
    ];
@endphp
<div class="min-h-screen md:flex">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white md:sticky md:top-0 md:flex md:h-screen">
        <div class="border-b border-slate-200 px-5 py-4 text-lg font-semibold text-blue-700">{{ config('app.name') }}</div>
        <nav class="flex-1 space-y-1 p-3">
            @foreach ($menu as [$label, $url, $pattern, $roles])
                @if (in_array($user->role->value, $roles, true))
                    <a href="{{ $url }}" class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($pattern) ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">{{ $label }}</a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 md:px-6">
            <button type="button" id="sidebar-toggle" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm md:hidden">Menu</button>
            <div class="ml-auto flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-medium leading-tight">{{ $user->name }}</p>
                    <p class="text-xs capitalize text-slate-500">{{ $user->role->value }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm text-white hover:bg-slate-700">Keluar</button>
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
    document.getElementById('sidebar-toggle').addEventListener('click', () => {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
    });
</script>
</body>
</html>