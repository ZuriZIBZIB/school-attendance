@props(['code', 'title', 'message'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} - {{ $title }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 p-4 text-center">
    <div>
        <p class="text-5xl font-semibold text-blue-600">{{ $code }}</p>
        <h1 class="mt-3 text-xl font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Kembali ke beranda</a>
    </div>
</body>
</html>