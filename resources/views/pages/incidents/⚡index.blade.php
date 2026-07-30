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

new #[Title('incidents.index.title')] class extends Component {
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
            <flux:heading size="xl">{{ __('incidents.index.title') }}</flux:heading>
            <flux:text>{{ __('incidents.index.description') }}</flux:text>
        </div>

        @auth
            <flux:button :href="route('incidents.create')" variant="primary" wire:navigate>
                {{ __('incidents.create.title') }}
            </flux:button>
        @endauth
    </header>

    <div class="grid gap-4 rounded-xl border border-orange-200 bg-white p-5 md:grid-cols-2 xl:grid-cols-4 dark:border-orange-900 dark:bg-zinc-900">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :label="__('common.fields.search')"
            type="search"
            :placeholder="__('incidents.filters.search_placeholder')"
        />

        <flux:select wire:model.live="status" :label="__('common.fields.status')">
            <flux:select.option value="">{{ __('incidents.filters.all_statuses') }}</flux:select.option>

            @foreach (IncidentStatus::cases() as $statusOption)
                <flux:select.option :value="$statusOption->value">
                    {{ __('incidents.statuses.'.$statusOption->value) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="kaijuId" :label="__('common.fields.kaiju')">
            <flux:select.option value="">{{ __('incidents.filters.all_kaijus') }}</flux:select.option>

            @foreach ($this->kaijus as $kaiju)
                <flux:select.option :value="$kaiju->id">
                    {{ $kaiju->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="sort" :label="__('incidents.filters.occurrence_order')">
            <flux:select.option value="newest">{{ __('incidents.filters.newest_first') }}</flux:select.option>
            <flux:select.option value="oldest">{{ __('incidents.filters.oldest_first') }}</flux:select.option>
        </flux:select>

        @if ($this->hasActiveFilters)
            <div class="md:col-span-2 xl:col-span-4">
                <flux:button wire:click="clearFilters" variant="ghost">
                    {{ __('common.actions.clear_filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    @if ($this->incidents->total() === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            @if ($this->hasActiveFilters)
                <flux:heading size="lg">{{ __('incidents.index.empty_filtered_heading') }}</flux:heading>
                <flux:text class="mt-2">{{ __('incidents.index.empty_filtered_description') }}</flux:text>
            @else
                <flux:heading size="lg">{{ __('incidents.index.empty_heading') }}</flux:heading>
                <flux:text class="mt-2">{{ __('incidents.index.empty_description') }}</flux:text>
            @endif
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->incidents as $incident)
                <article wire:key="incident-{{ $incident->id }}" class="flex flex-col gap-4 rounded-xl border border-zinc-200 border-t-4 border-t-orange-500 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-zinc-700 dark:border-t-orange-500 dark:bg-zinc-900 dark:shadow-black/20">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold tracking-wider text-orange-700 uppercase dark:text-orange-300">
                                {{ __('incidents.index.record') }}
                            </p>
                            <flux:heading size="lg">{{ $incident->title }}</flux:heading>
                        </div>

                        <flux:badge :color="config()->string('kers.badges.incident_statuses.'.$incident->status->value)">
                            {{ __('incidents.statuses.'.$incident->status->value) }}
                        </flux:badge>
                    </div>

                    <div class="space-y-1">
                        <flux:text>{{ $incident->location }}</flux:text>
                        <flux:text>
                            {{ __('common.labels.occurred', ['date' => $incident->occurred_at->locale(app()->getLocale())->isoFormat('LLL').' '.__('common.labels.utc')]) }}
                        </flux:text>
                    </div>

                    <div class="mt-auto flex flex-wrap items-center gap-2">
                        <flux:button :href="route('incidents.show', $incident)" variant="primary" wire:navigate>
                            {{ __('common.actions.view_details') }}
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
