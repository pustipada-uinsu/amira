<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title') | AMIRA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Aman: ambil dari app.js --}}
    @vite(['resources/js/app.js'])
</head>

<body
    class="relative min-h-screen overflow-hidden
           flex items-center justify-center
           bg-gradient-to-br
           from-emerald-50 via-emerald-100 to-green-200">

    <!-- PATTERN HALUS -->
    <div class="pointer-events-none absolute inset-0
                bg-[url('/images/bg-6.png')]
                bg-repeat
                bg-[length:500px_500px]
                opacity-5">
    </div>

    <!-- GLOW DEKORATIF -->
    <div
        class="pointer-events-none absolute -top-32 -left-32
               h-96 w-96 rounded-full
               bg-emerald-300/40 blur-3xl">
    </div>

    <div
        class="pointer-events-none absolute bottom-0 right-0
               h-[28rem] w-[28rem] rounded-full
               bg-emerald-200/40 blur-3xl">
    </div>

    <!-- CONTENT -->
    <main class="relative z-10 w-full max-w-md px-6">
        <div
            class="bg-white
                rounded-2xl shadow-xl
                p-8 text-center">
            @yield('content')
        </div>
    </main>

</body>
</html>
