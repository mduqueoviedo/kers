<?php

use App\Enums\KaijuCategory;
use App\Models\Kaiju;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('kaijus.create.title')] class extends Component {
    public string $name = '';

    public string $category = '';

    public string $threat_level = '';

    public string $description = '';

    /**
     * Get the validation rules for a new kaiju.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Kaiju::class, 'name')],
            'category' => ['required', Rule::enum(KaijuCategory::class)],
            'threat_level' => ['required', 'integer', 'between:1,5'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Validate and store a new kaiju.
     */
    public function save(): void
    {
        $validated = $this->validate();
        $validated['description'] = filled($validated['description'])
            ? $validated['description']
            : null;

        Kaiju::query()->create($validated);

        Flux::toast(__('kaijus.success.created'));

        $this->redirectRoute('kaijus.index', navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('kaijus.create.title') }}</flux:heading>
        <flux:text>{{ __('kaijus.create.description') }}</flux:text>
    </header>

    <form wire:submit="save" class="space-y-6" novalidate>
        <flux:input wire:model="name" :label="__('common.fields.name')" type="text" maxlength="255" required autofocus />

        <flux:select wire:model="category" :label="__('common.fields.category')" required>
            <flux:select.option value="">{{ __('kaijus.select_category') }}</flux:select.option>

            @foreach (KaijuCategory::cases() as $categoryOption)
                <flux:select.option :value="$categoryOption->value">
                    {{ __('kaijus.categories.'.$categoryOption->value) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="threat_level" :label="__('common.fields.threat_level')" type="number" min="1" max="5" required />

        <flux:textarea wire:model="description" :label="__('common.fields.description')"
            :description="__('kaijus.create.form_description')" rows="5" />

        <div class="flex items-center justify-end gap-3">
            <flux:button :href="route('kaijus.index')" variant="ghost" wire:navigate>
                {{ __('common.actions.cancel') }}
            </flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('kaijus.create.title') }}
            </flux:button>
        </div>
    </form>
</section>
