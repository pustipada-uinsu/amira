@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')

@section('content')

<!-- Decorative blur -->
<div class="absolute -top-16 -right-16 w-40 h-40 bg-yellow-300/30 rounded-full blur-3xl"></div>
<div class="absolute -bottom-16 -left-16 w-40 h-40 bg-orange-300/20 rounded-full blur-3xl"></div>

<!-- Logo -->
<div class="relative flex justify-center mb-6">
    <div class="p-3 rounded-2xl bg-yellow-50 ring-1 ring-yellow-100 shadow-sm">
        <img
            src="{{ asset('images/logo-amira.png') }}"
            alt="Logo AMIRA"
            class="h-16 w-auto">
    </div>
</div>

<!-- Error Code -->
<h1 class="relative text-6xl font-extrabold tracking-tight">
    429
</h1>

<!-- Title -->
<p class="mt-4 text-xl font-semibold text-gray-800">
    Terlalu Banyak Percobaan
</p>

<!-- Description -->
<p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
    Anda telah melakukan terlalu banyak permintaan dalam waktu singkat.
    Demi keamanan akun, silakan tunggu beberapa menit sebelum mencoba kembali.
</p>

<!-- Divider -->
<div class="mt-6 flex justify-center">
    <span class="h-px w-24 bg-gradient-to-r from-transparent via-yellow-300 to-transparent"></span>
</div>

<!-- Actions -->
<div class="mt-8 flex flex-col gap-3">
    <a href="{{ route('login') }}"
        class="inline-flex items-center justify-center gap-2
                  rounded-xl bg-yellow-500 px-5 py-3
                  text-white font-semibold
                  shadow-md shadow-yellow-500/30
                  hover:bg-yellow-600 hover:shadow-lg
                  transition">
        <!-- Clock icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Kembali ke Login
    </a>

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
    Demi keamanan sistem AMIRA, pembatasan ini diterapkan secara otomatis.
</p>

@endsection