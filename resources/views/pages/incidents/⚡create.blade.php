<?php

use App\Enums\IncidentStatus;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('incidents.create.title')] class extends Component {
    public string $title = '';

    public string $description = '';

    public string $location = '';

    public string $status = IncidentStatus::Open->value;

    public string $occurred_at = '';

    #[Url(as: 'kaiju', except: '')]
    public string $kaiju_id = '';

    /**
     * Get the known Kaijus available for incident assignment.
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
     * Get the validation rules for a new incident.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(IncidentStatus::class)],
            'occurred_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'kaiju_id' => ['bail', 'required', 'integer', Rule::exists(Kaiju::class, 'id')],
        ];
    }

    /**
     * Validate and store a new incident through its Kaiju relationship.
     */
    public function save(): void
    {
        $validated = $this->validate();
        $kaiju = Kaiju::query()->findOrFail((int) $validated['kaiju_id']);

        $kaiju->incidents()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'occurred_at' => CarbonImmutable::parse($validated['occurred_at'], 'UTC'),
        ]);

        Flux::toast(__('incidents.success.created'));

        $this->redirectRoute('kaijus.show', $kaiju, navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('incidents.create.title') }}</flux:heading>
        <flux:text>{{ __('incidents.create.description') }}</flux:text>
    </header>

    @if ($this->kaijus->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('incidents.create.empty_heading') }}</flux:heading>
            <flux:text class="mt-2">{{ __('incidents.create.empty_description') }}</flux:text>

            <flux:button :href="route('kaijus.create')" variant="primary" class="mt-6" wire:navigate>
                {{ __('kaijus.create.title') }}
            </flux:button>
        </div>
    @else
        <form wire:submit="save" class="space-y-6" novalidate>
            <flux:select wire:model="kaiju_id" :label="__('common.fields.kaiju')" required>
                <flux:select.option value="">{{ __('incidents.select_kaiju') }}</flux:select.option>

                @foreach ($this->kaijus as $kaiju)
                    <flux:select.option :value="$kaiju->id">
                        {{ $kaiju->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="title" :label="__('common.fields.title')" type="text" maxlength="255" required autofocus />

            <flux:textarea wire:model="description" :label="__('common.fields.description')" rows="5" required />

            <flux:input wire:model="location" :label="__('common.fields.location')" type="text" maxlength="255" required />

            <flux:select wire:model="status" :label="__('common.fields.status')" required>
                @foreach (IncidentStatus::cases() as $statusOption)
                    <flux:select.option :value="$statusOption->value">
                        {{ __('incidents.statuses.'.$statusOption->value) }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="occurred_at"
                :label="__('common.fields.occurred_at_utc')"
                type="datetime-local"
                step="60"
                required
            />

            <div class="flex items-center justify-end gap-3">
                <flux:button :href="route('kaijus.index')" variant="ghost" wire:navigate>
                    {{ __('common.actions.cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ __('incidents.create.title') }}
                </flux:button>
            </div>
        </form>
    @endif
</section>
