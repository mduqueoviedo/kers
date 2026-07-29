<?php

use App\Models\Kaiju;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Kaiju details')] class extends Component {
    public Kaiju $kaiju;

    public bool $confirmingDeletion = false;

    /**
     * Initialize the page with the route-bound Kaiju.
     */
    public function mount(Kaiju $kaiju): void
    {
        $this->kaiju = $kaiju;
    }

    /**
     * Ask the user to confirm permanent deletion.
     */
    public function requestDeletion(): void
    {
        $this->confirmingDeletion = true;
    }

    /**
     * Cancel the pending deletion.
     */
    public function cancelDeletion(): void
    {
        $this->confirmingDeletion = false;
    }

    /**
     * Permanently delete the route-bound Kaiju.
     */
    public function deleteKaiju(): void
    {
        if (! $this->confirmingDeletion) {
            return;
        }

        $this->kaiju->delete();

        Flux::toast(__('Kaiju deleted successfully.'));

        $this->redirectRoute('kaijus.index', navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <div>
        <flux:button :href="route('kaijus.index')" variant="ghost" wire:navigate>
            {{ __('Back to catalogue') }}
        </flux:button>
    </div>

    <article class="flex flex-col gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <flux:heading size="xl">{{ $kaiju->name }}</flux:heading>
                <flux:text>{{ __('Known creature record') }}</flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <flux:button :href="route('kaijus.edit', $kaiju)" variant="primary" wire:navigate>
                    {{ __('Edit kaiju') }}
                </flux:button>

                <flux:button wire:click="requestDeletion" variant="danger">
                    {{ __('Delete kaiju') }}
                </flux:button>
            </div>
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

    <flux:modal wire:model="confirmingDeletion" name="confirm-kaiju-deletion" class="md:w-120">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Delete kaiju?') }}</flux:heading>
                <flux:text>
                    {{ __('Are you sure you want to delete :name? This action cannot be undone.', ['name' => $kaiju->name]) }}
                </flux:text>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button wire:click="cancelDeletion" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button wire:click="deleteKaiju" variant="danger">
                    {{ __('Delete kaiju') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
