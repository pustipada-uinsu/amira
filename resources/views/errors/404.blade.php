@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')

<!-- Decorative blur -->
<div class="absolute -top-16 -right-16 w-40 h-40 bg-emerald-300/30 rounded-full blur-3xl"></div>
<div class="absolute -bottom-16 -left-16 w-40 h-40 bg-green-400/20 rounded-full blur-3xl"></div>

<!-- Logo -->
<div class="relative flex justify-center mb-6">
    <div class="p-3 rounded-2xl bg-emerald-50 ring-1 ring-emerald-100 shadow-sm">
        <img
            src="{{ asset('images/logo-amira.png') }}"
            alt="Logo AMIRA"
            class="h-16 w-auto">
    </div>
</div>

<!-- Error Code -->
<h1 class="relative text-6xl font-extrabold tracking-tight">
    404
</h1>

<!-- Title -->
<p class="mt-4 text-xl font-semibold text-gray-800">
    Halaman tidak ditemukan
</p>

<!-- Description -->
<p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
    Halaman yang Anda akses tidak tersedia, sudah dipindahkan,
    atau URL tidak sesuai. Silakan kembali ke sistem AMIRA.
</p>

<!-- Divider -->
<div class="mt-6 flex justify-center">
    <span class="h-px w-24 bg-gradient-to-r from-transparent via-emerald-300 to-transparent"></span>
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

    <a href="{{ route('login') }}"
        class="inline-flex items-center justify-center gap-2
                  rounded-xl border border-emerald-600
                  px-5 py-3
                  text-emerald-700 font-semibold
                  hover:bg-emerald-50
                  transition">
        Login AMIRA
    </a>
</div>

<!-- Footer hint -->
<p class="mt-8 text-xs text-gray-400">
    © {{ date('Y') }} AMIRA · Aplikasi Manajemen Informasi & Registrasi Acara
</p>

@endsection