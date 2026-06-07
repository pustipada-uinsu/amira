<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0d6efd">


        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" type="image/png" sizes="32x32" href="/images/logo-uinsu-new-kecil.png" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        <script>
            let deferredPrompt = null;

            window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            window.dispatchEvent(new Event('pwa-install-available'));
            });

            window.installPWA = async function () {
            if (!deferredPrompt) return;

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;

            deferredPrompt = null;
            };
        </script>


    </body>
</html>
