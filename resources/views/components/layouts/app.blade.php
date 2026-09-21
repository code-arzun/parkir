<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ $title ?? 'Parkir' }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Link Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

    <!-- HTML5 QR Code Library Core -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f1f5f9; }
        .active-tab { border-bottom: 3px solid #2563eb; color: #2563eb; font-weight: bold; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex justify-center items-start sm:py-6">

    <!-- Mobile Frame Container -->
    <div class="w-full max-w-md bg-white min-h-screen sm:min-h-[844px] sm:rounded-3xl shadow-2xl overflow-hidden flex flex-col relative pb-20">

        <!-- Slot Header Banner -->
        @if(isset($header))
            {{ $header }}
        @endif

        <!-- Slot Navigation Tabs -->
        @if(isset($navigation))
            {{ $navigation }}
        @endif

        <!-- Slot Main Content Area -->
        <main class="p-4 flex-1 overflow-y-auto">
            {{ $slot }}
        </main>

    </div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(reg => console.log('Service Worker Registered: ', reg.scope))
                    .catch(err => console.error('Service Worker Error: ', err));
            });
        }
    </script>

    <!-- Extra Custom Scripts Slot -->
    @if(isset($scripts))
        {{ $scripts }}
    @endif
</body>
</html>