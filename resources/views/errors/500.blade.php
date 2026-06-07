@extends('errors.layout')

@section('title', 'Kesalahan Sistem')

@section('content')

<!-- Logo -->
<div class="flex justify-center mb-6">
    <div class="p-3 rounded-2xl bg-gray-100 ring-1 ring-gray-200 shadow-sm">
        <img
            src="{{ asset('images/logo-amira.png') }}"
            alt="Logo AMIRA"
            class="h-14 w-auto">
    </div>
</div>

<!-- Error Code -->
<h1 class="text-6xl font-extrabold tracking-tight text-gray-700">
    500
</h1>

<!-- Title -->
<p class="mt-4 text-xl font-semibold text-gray-800">
    Terjadi Kesalahan Sistem
</p>

<!-- Description -->
<p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
    Sistem AMIRA sedang mengalami gangguan internal.
    Tim teknis kami telah menerima laporan ini.
    Silakan coba kembali beberapa saat lagi.
</p>

<!-- Divider -->
<div class="mt-6 flex justify-center">
    <span class="h-px w-24 bg-gradient-to-r from-transparent via-gray-300 to-transparent"></span>
</div>

<!-- Actions -->
<div class="mt-8 flex flex-col gap-3">
    <a href="{{ url('/dashboard') }}"
        class="inline-flex items-center justify-center gap-2
                  rounded-xl bg-emerald-600 px-5 py-3
                  text-white font-semibold
                  shadow-md shadow-emerald-600/30
                  hover:bg-emerald-700 hover:shadow-lg
                  transition">
        <!-- icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10" />
        </svg>
        Beranda
    </a>

</div>

<!-- Footer hint -->
<p class="mt-8 text-xs text-gray-400">
    © {{ date('Y') }} AMIRA · Aplikasi Manajemen Informasi & Registrasi Acara
</p>

@endsection