<?php

use App\Models\Incident;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Incident details')] class extends Component {
    public Incident $incident;

    public bool $confirmingDeletion = false;

    /**
     * Initialize the page with the route-bound Incident and its Kaiju.
     */
    public function mount(Incident $incident): void
    {
        $this->incident = $incident->load('kaiju');
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
     * Permanently delete the route-bound Incident.
     */
    public function deleteIncident(): void
    {
        if (!$this->confirmingDeletion) {
            return;
        }

        $this->incident->delete();

        Flux::toast(__('Incident deleted successfully.'));

        $this->redirectRoute('incidents.index', navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <flux:button :href="route('incidents.index')" variant="ghost" wire:navigate>
            {{ __('Back to incident catalogue') }}
        </flux:button>

        <div class="flex flex-wrap items-center gap-2">
            <flux:button :href="route('incidents.edit', $incident)" variant="primary" wire:navigate>
                {{ __('Edit incident') }}
            </flux:button>

            <flux:button wire:click="requestDeletion" variant="danger">
                {{ __('Delete incident') }}
            </flux:button>
        </div>
    </div>

    <article class="flex flex-col gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <flux:heading size="xl">{{ $incident->title }}</flux:heading>
                <flux:text>{{ __('Recorded incident') }}</flux:text>
            </div>

            <flux:badge :color="config()->string('kers.badges.incident_statuses.'.$incident->status->value)">
                {{ ucfirst($incident->status->value) }}
            </flux:badge>
        </header>

        <dl class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Location') }}</dt>
                <dd class="text-zinc-900 dark:text-white">{{ $incident->location }}</dd>
            </div>

            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Occurred at') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    <time datetime="{{ $incident->occurred_at->toIso8601String() }}">
                        {{ $incident->occurred_at->format('M j, Y, H:i') }} UTC
                    </time>
                </dd>
            </div>
        </dl>

        <div class="space-y-2 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('Description') }}</flux:heading>
            <flux:text>{{ $incident->description }}</flux:text>
        </div>

        <div class="space-y-2 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('Kaiju involved') }}</flux:heading>
            <flux:button :href="route('kaijus.show', $incident->kaiju)" variant="outline" wire:navigate>
                {{ $incident->kaiju->name }}
            </flux:button>
        </div>

        <dl class="grid gap-4 border-t border-zinc-200 pt-6 sm:grid-cols-2 dark:border-zinc-700">
            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Created at') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    <time datetime="{{ $incident->created_at?->toIso8601String() }}">
                        {{ $incident->created_at?->format('M j, Y, H:i') }} UTC
                    </time>
                </dd>
            </div>

            <div class="space-y-1">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Updated at') }}</dt>
                <dd class="text-zinc-900 dark:text-white">
                    <time datetime="{{ $incident->updated_at?->toIso8601String() }}">
                        {{ $incident->updated_at?->format('M j, Y, H:i') }} UTC
                    </time>
                </dd>
            </div>
        </dl>
    </article>

    <flux:modal wire:model="confirmingDeletion" name="confirm-incident-deletion" class="md:w-120">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">{{ __('Delete incident?') }}</flux:heading>
                <flux:text>
                    {{ __('Are you sure you want to delete :title? This action cannot be undone.', ['title' => $incident->title]) }}
                </flux:text>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button wire:click="cancelDeletion" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button wire:click="deleteIncident" variant="danger">
                    {{ __('Delete incident') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
