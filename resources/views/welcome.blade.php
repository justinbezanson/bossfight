<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite('resources/css/app.css')
    </head>
    <body class="font-sans antialiased bg-background text-foreground min-h-screen flex flex-col">
        <header class="flex justify-end items-center gap-4 p-6">
            @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-xs transition-colors hover:bg-primary/90"
                >
                    Dashboard
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                >
                    Log in
                </a>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-xs transition-colors hover:bg-primary/90"
                >
                    Register
                </a>
            @endauth
        </header>

        <main class="flex-1 flex flex-col items-center justify-center px-4">
            <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">Welcome</h1>
            <p class="mt-4 text-lg text-muted-foreground text-center max-w-md">
                {{ config('app.name', 'Your Application') }}
            </p>
        </main>
    </body>
</html>
