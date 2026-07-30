<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    @php
        $isKaijuArea = request()->routeIs('kaijus.*');
        $isIncidentArea = request()->routeIs('incidents.*');

        $areaLabel = match (true) {
            $isKaijuArea => __('Kaiju registry'),
            $isIncidentArea => __('Incident operations'),
            default => null,
        };

        $areaDescription = match (true) {
            $isKaijuArea => __('Known creature records'),
            $isIncidentArea => __('Recorded emergency activity'),
            default => null,
        };
    @endphp

    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 antialiased dark:bg-zinc-950">
        <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('kaijus.index') }}" class="flex items-center gap-2.5 font-semibold text-zinc-900 dark:text-white" wire:navigate>
                    <img src="{{ asset('favicon.svg') }}" width="28" height="28" alt="">
                    <span>{{ config('app.name', 'KERS') }}</span>
                </a>

                <div class="flex items-center gap-1">
                    <a
                        href="{{ route('kaijus.index') }}"
                        @class([
                            'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                            'bg-teal-100 text-teal-900 dark:bg-teal-950 dark:text-teal-200' => $isKaijuArea,
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $isKaijuArea,
                        ])
                        @if ($isKaijuArea) aria-current="page" @endif
                        wire:navigate
                    >
                        {{ __('Kaijus') }}
                    </a>

                    <a
                        href="{{ route('incidents.index') }}"
                        @class([
                            'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                            'bg-orange-100 text-orange-900 dark:bg-orange-950 dark:text-orange-200' => $isIncidentArea,
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $isIncidentArea,
                        ])
                        @if ($isIncidentArea) aria-current="page" @endif
                        wire:navigate
                    >
                        {{ __('Incidents') }}
                    </a>
                </div>
            </nav>
        </header>

        @if ($areaLabel !== null)
            <div
                @class([
                    'border-b',
                    'border-teal-200 bg-teal-50/80 dark:border-teal-900 dark:bg-teal-950/40' => $isKaijuArea,
                    'border-orange-200 bg-orange-50/80 dark:border-orange-900 dark:bg-orange-950/40' => $isIncidentArea,
                ])
            >
                <div class="mx-auto flex max-w-7xl items-center gap-3 px-6 py-3 lg:px-8">
                    <span
                        @class([
                            'h-2.5 w-2.5 shrink-0 rounded-full',
                            'bg-teal-500' => $isKaijuArea,
                            'bg-orange-500' => $isIncidentArea,
                        ])
                        aria-hidden="true"
                    ></span>

                    <div class="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:gap-3">
                        <p
                            @class([
                                'text-xs font-semibold tracking-wider uppercase',
                                'text-teal-800 dark:text-teal-200' => $isKaijuArea,
                                'text-orange-800 dark:text-orange-200' => $isIncidentArea,
                            ])
                        >
                            {{ $areaLabel }}
                        </p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $areaDescription }}</p>
                    </div>
                </div>
            </div>
        @endif

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
