<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @php
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $appEntry = $manifest['resources/js/app.js'] ?? null;
            }
        @endphp

        @if(isset($appEntry))
            {{-- CSS --}}
            @if(isset($appEntry['css']))
                @foreach($appEntry['css'] as $css)
                    <link rel="stylesheet" href="{{ secure_asset('build/' . $css) }}">
                @endforeach
            @endif

            {{-- Script principal --}}
            <script type="module" src="{{ secure_asset('build/' . $appEntry['file']) }}"></script>

            {{-- Données Inertia --}}
            <script>
                window.Laravel = {
                    csrfToken: '{{ csrf_token() }}',
                };
            </script>
        @else
            {{-- Fallback vers Vite --}}
            @vite(['resources/js/app.js'])
        @endif        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
