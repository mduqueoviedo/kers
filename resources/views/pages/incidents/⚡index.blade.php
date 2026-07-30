<?php

use App\Models\Incident;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Incident catalogue')] class extends Component {
    use WithPagination;

    /**
     * Get the current page of incidents ordered by occurrence time.
     *
     * @return LengthAwarePaginator<int, Incident>
     */
    #[Computed]
    public function incidents(): LengthAwarePaginator
    {
        return Incident::query()
            ->with('kaiju')
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate(config()->integer('kers.pagination.incidents_per_page'));
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-2">
            <flux:heading size="xl">{{ __('Incident catalogue') }}</flux:heading>
            <flux:text>{{ __('Recorded Kaiju incidents ordered by most recent occurrence.') }}</flux:text>
        </div>

        <flux:button :href="route('incidents.create')" variant="primary" wire:navigate>
            {{ __('Record incident') }}
        </flux:button>
    </header>

    @if ($this->incidents->total() === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('No incidents have been recorded.') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Recorded Kaiju activity will appear here.') }}</flux:text>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->incidents as $incident)
                <article wire:key="incident-{{ $incident->id }}" class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="flex items-start justify-between gap-4">
                        <flux:heading size="lg">{{ $incident->title }}</flux:heading>
                        <flux:badge :color="config()->string('kers.badges.incident_statuses.'.$incident->status->value)">
                            {{ ucfirst($incident->status->value) }}
                        </flux:badge>
                    </div>

                    <div class="space-y-1">
                        <flux:text>{{ $incident->location }}</flux:text>
                        <flux:text>
                            {{ __('Occurred :date', ['date' => $incident->occurred_at->format('M j, Y, H:i').' UTC']) }}
                        </flux:text>
                    </div>

                    <flux:button
                        :href="route('kaijus.show', $incident->kaiju)"
                        variant="ghost"
                        class="mt-auto self-start"
                        wire:navigate
                    >
                        {{ $incident->kaiju->name }}
                    </flux:button>
                </article>
            @endforeach
        </div>

        @if ($this->incidents->hasPages())
            <div>
                {{ $this->incidents->links() }}
            </div>
        @endif
    @endif
</section>
