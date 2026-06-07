@extends('errors.layout')

@section('title', 'Akses Dibatasi')

@section('content')

<!-- Decorative blur -->
<div class="absolute -top-16 -right-16 w-40 h-40 bg-amber-300/30 rounded-full blur-3xl"></div>
<div class="absolute -bottom-16 -left-16 w-40 h-40 bg-orange-400/20 rounded-full blur-3xl"></div>

<!-- Logo -->
<div class="relative flex justify-center mb-6">
    <div class="p-3 rounded-2xl bg-amber-50 ring-1 ring-amber-100 shadow-sm">
        <img
            src="{{ asset('images/logo-amira.png') }}"
            alt="Logo AMIRA"
            class="h-16 w-auto">
    </div>
</div>

<!-- Error Code -->
<h1 class="relative text-6xl font-extrabold tracking-tight">
    403
</h1>

<!-- Title -->
<p class="mt-4 text-xl font-semibold text-gray-800">
    Forbidden
</p>

<div class="mt-4 max-w-md mx-auto">
    <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 shadow-sm">

        <!-- ICON -->
        <div class="text-red-500 text-xl">
            ⚠️
        </div>

        <!-- MESSAGE -->
        <p class="text-sm font-medium text-red-700 leading-relaxed">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>

    </div>
</div>

<!-- Description -->
<!-- <p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
        Anda tidak memiliki izin untuk mengakses halaman ini.
        Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator sistem.
    </p> -->

<!-- Divider -->
<div class="mt-6 flex justify-center">
    <span class="h-px w-24 bg-gradient-to-r from-transparent via-amber-300 to-transparent"></span>
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