<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit kaiju')] class extends Component {
    public Kaiju $kaiju;

    public string $name = '';

    public string $category = '';

    public string $threat_level = '';

    public string $description = '';

    /**
     * Initialize the form with the route-bound Kaiju.
     */
    public function mount(Kaiju $kaiju): void
    {
        $this->kaiju = $kaiju;
        $this->name = $kaiju->name;
        $this->category = $kaiju->category->value;
        $this->threat_level = (string) $kaiju->threat_level;
        $this->description = $kaiju->description ?? '';
    }

    /**
     * Get the validation rules for updating the kaiju.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Kaiju::class, 'name')->ignore($this->kaiju),
            ],
            'category' => ['required', Rule::enum(KaijuCategory::class)],
            'threat_level' => ['required', 'integer', 'between:1,5'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Validate and persist the kaiju changes.
     */
    public function save(): void
    {
        $validated = $this->validate();
        $validated['description'] = filled($validated['description'])
            ? $validated['description']
            : null;

        $this->kaiju->update($validated);

        Flux::toast(__('Kaiju updated successfully.'));

        $this->redirectRoute('kaijus.show', $this->kaiju, navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('Edit kaiju') }}</flux:heading>
        <flux:text>{{ __('Correct the known details for this creature.') }}</flux:text>
    </header>

    <form wire:submit="save" class="space-y-6" novalidate>
        <flux:input wire:model="name" :label="__('Name')" type="text" maxlength="255" required autofocus />

        <flux:select wire:model="category" :label="__('Category')" required>
            <flux:select.option value="">{{ __('Select a category') }}</flux:select.option>

            @foreach (KaijuCategory::cases() as $categoryOption)
                <flux:select.option :value="$categoryOption->value">
                    {{ ucfirst($categoryOption->value) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="threat_level" :label="__('Threat level')" type="number" min="1" max="5" required />

        <flux:textarea wire:model="description" :label="__('Description')"
            :description="__('Optional observations about the creature.')" rows="5" />

        <div class="flex items-center justify-end gap-3">
            <flux:button :href="route('kaijus.show', $kaiju)" variant="ghost" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('Save changes') }}
            </flux:button>
        </div>
    </form>
</section>
