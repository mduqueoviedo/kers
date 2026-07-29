<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        <header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('kaijus.index') }}" class="font-semibold text-zinc-900 dark:text-white" wire:navigate>
                    {{ config('app.name', 'KERS') }}
                </a>

                <a
                    href="{{ route('kaijus.index') }}"
                    class="text-sm font-medium text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white"
                    wire:navigate
                >
                    {{ __('Kaijus') }}
                </a>
            </nav>
        </header>

        <main class="mx-auto w-full max-w-7xl px-6 py-8 lg:px-8">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
