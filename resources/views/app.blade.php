<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- PrimeVue Aura 主題的基底 surface 色，防止 JS 注入前的白框閃爍 -->
        <style>
            :root {
                color-scheme: light;
                background-color: #ffffff;
            }
            html, body {
                background-color: #ffffff;
                margin: 0;
            }
        </style>

        <!-- Scripts -->
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
