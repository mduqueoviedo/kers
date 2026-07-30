<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Kaiju catalogue')] class extends Component {
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    #[Url(as: 'threat', except: '')]
    public string $threatLevel = '';

    /**
     * Reset pagination when the name search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the category filter changes.
     */
    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when the threat-level filter changes.
     */
    public function updatedThreatLevel(): void
    {
        $this->resetPage();
    }

    /**
     * Remove all catalogue search and filter criteria.
     */
    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'threatLevel');
        $this->resetPage();
    }

    /**
     * Determine whether the catalogue has active search or filter criteria.
     */
    #[Computed]
    public function hasActiveFilters(): bool
    {
        return trim($this->search) !== ''
            || $this->category !== ''
            || $this->threatLevel !== '';
    }

    /**
     * Get the current page of the kaiju catalogue.
     *
     * @return LengthAwarePaginator<int, Kaiju>
     */
    #[Computed]
    public function kaijus(): LengthAwarePaginator
    {
        $search = trim($this->search);
        $threatLevel = in_array($this->threatLevel, ['1', '2', '3', '4', '5'], true)
            ? (int) $this->threatLevel
            : -1;

        return Kaiju::query()
            ->when(
                $search !== '',
                fn ($query) => $query->whereLike('name', "%{$search}%"),
            )
            ->when(
                $this->category !== '',
                fn ($query) => $query->where('category', $this->category),
            )
            ->when(
                $this->threatLevel !== '',
                fn ($query) => $query->where('threat_level', $threatLevel),
            )
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

        <div class="flex flex-wrap items-center gap-2">
            <flux:button :href="route('incidents.create')" variant="outline" wire:navigate>
                {{ __('Record incident') }}
            </flux:button>

            <flux:button :href="route('kaijus.create')" variant="primary" wire:navigate>
                {{ __('Register kaiju') }}
            </flux:button>
        </div>
    </header>

    <div class="grid gap-4 rounded-xl border border-teal-200 bg-white p-5 md:grid-cols-3 dark:border-teal-900 dark:bg-zinc-900">
        <flux:input
            wire:model.live.debounce.300ms="search"
            :label="__('Search')"
            type="search"
            :placeholder="__('Search by name')"
        />

        <flux:select wire:model.live="category" :label="__('Category')">
            <flux:select.option value="">{{ __('All categories') }}</flux:select.option>

            @foreach (KaijuCategory::cases() as $categoryOption)
                <flux:select.option :value="$categoryOption->value">
                    {{ ucfirst($categoryOption->value) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="threatLevel" :label="__('Threat level')">
            <flux:select.option value="">{{ __('All threat levels') }}</flux:select.option>

            @foreach (range(1, 5) as $threatLevelOption)
                <flux:select.option :value="$threatLevelOption">
                    {{ __('Level :level', ['level' => $threatLevelOption]) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        @if ($this->hasActiveFilters)
            <div class="md:col-span-3">
                <flux:button wire:click="clearFilters" variant="ghost">
                    {{ __('Clear filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    @if ($this->kaijus->total() === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            @if ($this->hasActiveFilters)
                <flux:heading size="lg">{{ __('No kaijus match the current search and filters.') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Try different criteria or clear the current filters.') }}</flux:text>
            @else
                <flux:heading size="lg">{{ __('No kaijus have been catalogued.') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Known creatures will appear here once they are registered.') }}</flux:text>
            @endif
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->kaijus as $kaiju)
                <article wire:key="kaiju-{{ $kaiju->id }}" class="flex flex-col gap-4 rounded-xl border border-zinc-200 border-t-4 border-t-teal-500 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-zinc-700 dark:border-t-teal-500 dark:bg-zinc-900 dark:shadow-black/20">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold tracking-wider text-teal-700 uppercase dark:text-teal-300">
                                {{ __('Known creature') }}
                            </p>
                            <flux:heading size="lg">{{ $kaiju->name }}</flux:heading>
                        </div>

                        <flux:badge :color="config()->string('kers.badges.kaiju_categories.'.$kaiju->category->value)">
                            {{ ucfirst($kaiju->category->value) }}
                        </flux:badge>
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
