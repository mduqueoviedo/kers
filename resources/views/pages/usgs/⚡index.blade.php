<?php

use App\Services\Usgs\UsgsEarthquakeClient;
use App\Services\Usgs\UsgsEarthquakeMapper;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('USGS events')] class extends Component {
    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    public ?string $error = null;

    public function mount(UsgsEarthquakeClient $client, UsgsEarthquakeMapper $mapper): void
    {
        try {
            $this->events = $mapper->map($client->fetchRecentEvents());
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            report($exception);
            $this->error = __('usgs.errors.unavailable');
        }
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('usgs.title') }}</flux:heading>
        <flux:text>{{ __('usgs.description') }}</flux:text>
    </header>

    @if ($error !== null)
        <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 dark:border-red-900 dark:bg-red-950/40" role="alert">
            <flux:heading size="lg">{{ __('usgs.errors.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ $error }}</flux:text>
        </div>
    @elseif (count($events) === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('usgs.empty.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ __('usgs.empty.description') }}</flux:text>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($events as $event)
                <article wire:key="usgs-event-{{ $event['id'] }}" class="flex flex-col gap-4 rounded-xl border border-sky-200 border-t-4 border-t-sky-500 bg-white p-5 shadow-sm dark:border-sky-900 dark:border-t-sky-500 dark:bg-zinc-900">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wider text-sky-700 uppercase dark:text-sky-300">
                            {{ __('usgs.event_label') }}
                        </p>
                        <flux:heading size="lg">{{ $event['title'] }}</flux:heading>
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.magnitude') }}</dt>
                            <dd class="font-medium text-zinc-900 dark:text-white">{{ $event['magnitude'] ?? __('usgs.not_available') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.occurred_at') }}</dt>
                            <dd class="text-right text-zinc-900 dark:text-white">{{ $event['occurred_at'] }}</dd>
                        </div>
                    </dl>

                    @if ($event['location'] !== null)
                        <flux:text>{{ $event['location'] }}</flux:text>
                    @endif

                    @if ($event['url'] !== null)
                        <flux:button :href="$event['url']" target="_blank" variant="ghost" class="mt-auto self-start">
                            {{ __('usgs.view_source') }}
                        </flux:button>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>
