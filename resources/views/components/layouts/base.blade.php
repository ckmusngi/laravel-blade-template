@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name', 'Laravel') }}</title>

        {{-- Apply the saved or system theme before the page paints, so there's no flash of the wrong theme. --}}
        <script>
            (() => {
                let theme = null;
                try { theme = localStorage.getItem('theme'); } catch (e) {}
                if (theme !== 'light' && theme !== 'dark') {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.dataset.theme = theme;
            })();
        </script>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes }}>
        {{ $slot }}
    </body>
</html>
