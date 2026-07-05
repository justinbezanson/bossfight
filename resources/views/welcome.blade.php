<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite('resources/css/app.css')
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col" style="background-color:#1f3149;color:#f8fafc">
        <header class="flex justify-end items-center gap-4 p-6">
            @auth
                <a
                    href="{{ route('dashboard') }}"
                    style="background-color:#d97706"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-xs transition-colors hover:bg-amber-700"
                >
                    Dashboard
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    style="border:1px solid rgba(255,255,255,0.3)"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white/80 transition-colors hover:border-white hover:text-white"
                >
                    Log in
                </a>
                <a
                    href="{{ route('register') }}"
                    style="background-color:#d97706"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-xs transition-colors hover:bg-amber-700"
                >
                    Register
                </a>
            @endauth
        </header>

        <main class="flex-1 flex flex-col items-center justify-center px-4">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                <img src="{{ asset('images/bossfight.png') }}" alt="{{ config('app.name', 'Your Application') }}" class="w-auto mx-auto" />
            </h1>
        </main>
    </body>
</html>
