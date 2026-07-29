<?php

use App\Models\Kaiju;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Kaiju catalogue')] class extends Component {
    use WithPagination;

    /**
     * Get the current page of the kaiju catalogue.
     *
     * @return LengthAwarePaginator<int, Kaiju>
     */
    #[Computed]
    public function kaijus(): LengthAwarePaginator
    {
        return Kaiju::query()
            ->orderBy('name')
            ->paginate(config()->integer('kers.pagination.kaijus_per_page'));
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-2">
            <flux:heading size="xl">{{ __('Kaiju catalogue') }}</flux:heading>
            <flux:text>{{ __('Known creatures monitored by the Kaiju Emergency Response System.') }}</flux:text>
        </div>

        <flux:button :href="route('kaijus.create')" variant="primary" wire:navigate>
            {{ __('Register kaiju') }}
        </flux:button>
    </header>

    @if ($this->kaijus->total() === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('No kaijus have been catalogued.') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Known creatures will appear here once they are registered.') }}</flux:text>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->kaijus as $kaiju)
                <article wire:key="kaiju-{{ $kaiju->id }}" class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="flex items-start justify-between gap-4">
                        <flux:heading size="lg">{{ $kaiju->name }}</flux:heading>
                        <flux:badge>{{ ucfirst($kaiju->category->value) }}</flux:badge>
                    </div>

                    <flux:text>{{ __('Threat level :level of 5', ['level' => $kaiju->threat_level]) }}</flux:text>

                    <flux:text>
                        {{ $kaiju->description ?? __('No description provided.') }}
                    </flux:text>

                    <flux:button
                        :href="route('kaijus.show', $kaiju)"
                        variant="ghost"
                        class="mt-auto self-start"
                        wire:navigate
                    >
                        {{ __('View details') }}
                    </flux:button>
                </article>
            @endforeach
        </div>

        @if ($this->kaijus->hasPages())
            <div>
                {{ $this->kaijus->links() }}
            </div>
        @endif
    @endif
</section>
