<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard {{ $role }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 p-8">
    <div class="mx-auto max-w-xl rounded-xl border border-slate-200 bg-white p-8">
        <h1 class="text-lg font-semibold text-slate-900">Dashboard {{ $role }}</h1>
        <p class="mt-1 text-sm text-slate-500">Halo, {{ auth()->user()->name }}. Halaman ini sementara.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm text-white">Keluar</button>
        </form>
    </div>
</body>
</html>