<?php

use App\Models\Kaiju;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kaiju details')] class extends Component {
    public Kaiju $kaiju;

    /**
     * Initialize the page with the route-bound Kaiju.
     */
    public function mount(Kaiju $kaiju): void
    {
        $this->kaiju = $kaiju;
    }
}; ?>

<section class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <div>
        <flux:button :href="route('kaijus.index')" variant="ghost" wire:navigate>
            {{ __('Back to catalogue') }}
        </flux:button>
    </div>

    <article class="flex flex-col gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <header class="space-y-2">
            <flux:heading size="xl">{{ $kaiju->name }}</flux:heading>
            <flux:text>{{ __('Known creature record') }}</flux:text>
        </header>

        <dl class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Category') }}</dt>
                <dd class="text-zinc-900 dark:text-white">{{ ucfirst($kaiju->category->value) }}</dd>
            </div>

            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Threat level') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    {{ __('Level :level of 5', ['level' => $kaiju->threat_level]) }}
                </dd>
            </div>
        </dl>

        <div class="space-y-2 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('Description') }}</flux:heading>
            <flux:text>{{ $kaiju->description ?? __('No description provided.') }}</flux:text>
        </div>

        <dl class="grid gap-4 border-t border-zinc-200 pt-6 sm:grid-cols-2 dark:border-zinc-700">
            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Created at') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    <time datetime="{{ $kaiju->created_at?->toIso8601String() }}">
                        {{ $kaiju->created_at?->isoFormat('LLL') }}
                    </time>
                </dd>
            </div>

            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Updated at') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    <time datetime="{{ $kaiju->updated_at?->toIso8601String() }}">
                        {{ $kaiju->updated_at?->isoFormat('LLL') }}
                    </time>
                </dd>
            </div>
        </dl>
    </article>
</section>
