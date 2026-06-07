@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem')

@section('content')

<style>
    @keyframes bounce-soft {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    @keyframes pulse-soft {

        0%,
        100% {
            opacity: .6;
        }

        50% {
            opacity: 1;
        }
    }
</style>

<!-- Logo -->
<div class="flex justify-center mb-6">
    <div class="p-3 rounded-2xl bg-gray-100 ring-1 ring-gray-200 shadow-sm">
        <img
            src="{{ asset('images/logo-amira.png') }}"
            alt="Logo AMIRA"
            class="h-14 w-auto">
    </div>
</div>


<!-- Emoji Animation -->
<div class="flex justify-center mb-2">
    <div
        class="text-6xl"
        style="animation: bounce-soft 1.8s ease-in-out infinite"
        aria-hidden="true">
        ⚙️
    </div>
</div>

<!-- Status -->
<p class="mt-4 text-sm font-medium text-amber-700"
    style="animation: pulse-soft 2s ease-in-out infinite">
    Mohon Bersabar…
</p>

<!-- Error Code -->
<h1 class="text-6xl font-extrabold tracking-tight text-amber-500 mt-5">
    503
</h1>

<!-- Title -->
<p class="mt-4 text-xl font-semibold text-gray-800">
    Sistem Sedang Dalam Pemeliharaan
</p>

<!-- Description -->
<p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
    Kami sedang melakukan peningkatan layanan agar AMIRA
    tetap stabil, aman, dan nyaman digunakan.
</p>


<!-- Action -->

<!-- Footer -->
<p class="mt-10 text-xs text-gray-400">
    © {{ date('Y') }} AMIRA · Aplikasi Manajemen Informasi & Registrasi Acara
</p>

@endsection