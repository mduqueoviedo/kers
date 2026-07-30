<?php

use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\Kaiju;
use Carbon\CarbonImmutable;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('incidents.edit.title')] class extends Component {
    public Incident $incident;

    public string $title = '';

    public string $description = '';

    public string $location = '';

    public string $status = '';

    public string $occurred_at = '';

    public string $kaiju_id = '';

    /**
     * Initialize the form with the route-bound Incident.
     */
    public function mount(Incident $incident): void
    {
        $this->incident = $incident;
        $this->title = $incident->title;
        $this->description = $incident->description;
        $this->location = $incident->location;
        $this->status = $incident->status->value;
        $this->occurred_at = $incident->occurred_at->format('Y-m-d\TH:i');
        $this->kaiju_id = (string) $incident->kaiju_id;
    }

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
     * Get the validation rules for updating the incident.
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
     * Validate and persist the Incident changes.
     */
    public function save(): void
    {
        abort_unless(auth()->check(), 403);

        $validated = $this->validate();

        $this->incident->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'occurred_at' => CarbonImmutable::parse($validated['occurred_at'], 'UTC'),
            'kaiju_id' => (int) $validated['kaiju_id'],
        ]);

        Flux::toast(__('incidents.success.updated'));

        $this->redirectRoute('incidents.show', $this->incident, navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('incidents.edit.title') }}</flux:heading>
        <flux:text>{{ __('incidents.edit.description') }}</flux:text>
    </header>

    <form wire:submit="save" class="space-y-6" novalidate>
        <flux:select wire:model="kaiju_id" :label="__('common.fields.kaiju')" required>
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
            <flux:button :href="route('incidents.show', $incident)" variant="ghost" wire:navigate>
                {{ __('common.actions.cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('common.actions.save_changes') }}
            </flux:button>
        </div>
    </form>
</section>
