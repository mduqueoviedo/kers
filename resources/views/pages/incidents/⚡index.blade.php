<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Incident catalogue')] class extends Component {
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(as: 'kaiju', except: '')]
    public string $kaijuId = '';

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    /**
     * Reset pagination when the search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the status filter changes.
     */
    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the Kaiju filter changes.
     */
    public function updatedKaijuId(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the occurrence ordering changes.
     */
    public function updatedSort(): void
    {
        $this->resetPage();
    }

    /**
     * Remove all catalogue criteria.
     */
    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'kaijuId', 'sort');
        $this->resetPage();
    }

    /**
     * Determine whether the catalogue has active criteria.
     */
    #[Computed]
    public function hasActiveFilters(): bool
    {
        return trim($this->search) !== ''
            || $this->status !== ''
            || $this->kaijuId !== ''
            || $this->sort !== 'newest';
    }

    /**
     * Get the known Kaijus available for filtering.
     *
     * @return Collection<int, Kaiju>
     */
    #[Computed]
    public function kaijus(): Collection
    {
        return Kaiju::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the current filtered and ordered page of incidents.
     *
     * @return LengthAwarePaginator<int, Incident>
     */
    #[Computed]
    public function incidents(): LengthAwarePaginator
    {
        $search = trim($this->search);
        $kaijuId = ctype_digit($this->kaijuId) ? (int) $this->kaijuId : -1;
        $oldestFirst = $this->sort === 'oldest';

        return Incident::query()
            ->with('kaiju')
            ->when(
                $search !== '',
                fn ($query) => $query->where(
                    fn ($query) => $query
                        ->whereLike('title', "%{$search}%")
                        ->orWhereLike('location', "%{$search}%"),
                ),
            )
            ->when(
                $this->status !== '',
                fn ($query) => $query->where('status', $this->status),
            )
            ->when(
                $this->kaijuId !== '',
                fn ($query) => $query->where('kaiju_id', $kaijuId),
            )
            ->when(
                $oldestFirst,
                fn ($query) => $query->orderBy('occurred_at')->orderBy('id'),
                fn ($query) => $query->orderByDesc('occurred_at')->orderByDesc('id'),
            )
            ->paginate(config()->integer('kers.pagination.incidents_per_page'));
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-2">
            <flux:heading size="xl">{{ __('Incident catalogue') }}</flux:heading>
            <flux:text>{{ __('Find recorded Kaiju incidents by their current details.') }}</flux:text>
        </div>

        <flux:button :href="route('incidents.create')" variant="primary" wire:navigate>
            {{ __('Record incident') }}
        </flux:button>
    </header>

    <div class="grid gap-4 rounded-xl border border-zinc-200 p-5 md:grid-cols-2 xl:grid-cols-4 dark:border-zinc-700">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :label="__('Search')"
            type="search"
            :placeholder="__('Search by title or location')"
        />

        <flux:select wire:model.live="status" :label="__('Status')">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>

            @foreach (IncidentStatus::cases() as $statusOption)
                <flux:select.option :value="$statusOption->value">
                    {{ ucfirst($statusOption->value) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="kaijuId" :label="__('Kaiju')">
            <flux:select.option value="">{{ __('All Kaijus') }}</flux:select.option>

            @foreach ($this->kaijus as $kaiju)
                <flux:select.option :value="$kaiju->id">
                    {{ $kaiju->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="sort" :label="__('Occurrence order')">
            <flux:select.option value="newest">{{ __('Newest first') }}</flux:select.option>
            <flux:select.option value="oldest">{{ __('Oldest first') }}</flux:select.option>
        </flux:select>

        @if ($this->hasActiveFilters)
            <div class="md:col-span-2 xl:col-span-4">
                <flux:button wire:click="clearFilters" variant="ghost">
                    {{ __('Clear filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    @if ($this->incidents->total() === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            @if ($this->hasActiveFilters)
                <flux:heading size="lg">{{ __('No incidents match the current search and filters.') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Try different criteria or clear the current filters.') }}</flux:text>
            @else
                <flux:heading size="lg">{{ __('No incidents have been recorded.') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Recorded Kaiju activity will appear here.') }}</flux:text>
            @endif
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

                    <div class="mt-auto flex flex-wrap items-center gap-2">
                        <flux:button :href="route('incidents.show', $incident)" variant="primary" wire:navigate>
                            {{ __('View details') }}
                        </flux:button>

                        <flux:button :href="route('kaijus.show', $incident->kaiju)" variant="ghost" wire:navigate>
                            {{ $incident->kaiju->name }}
                        </flux:button>
                    </div>
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
