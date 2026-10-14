@extends('layouts.app')
@section('title', 'Dashboard '.$role)
@section('content')
    <h1 class="text-xl font-semibold">Dashboard {{ $role }}</h1>
    <p class="mt-1 text-sm text-slate-500">Halo, {{ auth()->user()->name }}. Halaman ini sementara.</p>
@endsection