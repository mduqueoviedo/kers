<?php

use App\Enums\IncidentStatus;
use App\Models\Kaiju;
use App\Services\Usgs\UsgsEarthquakeClient;
use App\Services\Usgs\UsgsEarthquakeMapper;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('USGS events')] class extends Component {
    /** @var array<int, array<string, mixed>> */
    public array $events = [];

    public ?string $error = null;

    public ?string $selected_event_id = null;

    public string $kaiju_id = '';

    public function mount(UsgsEarthquakeClient $client, UsgsEarthquakeMapper $mapper): void
    {
        try {
            $this->events = $mapper->map($client->fetchRecentEvents());
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            report($exception);
            $this->error = __('usgs.errors.unavailable');
        }
    }

    /**
     * Get the known Kaijus available for the imported Incident.
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
     * Get validation rules for importing one USGS event.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'selected_event_id' => ['required', 'string'],
            'kaiju_id' => ['bail', 'required', 'integer', Rule::exists(Kaiju::class, 'id')],
        ];
    }

    /**
     * Fetch the current catalogue and create one Incident from the selected event.
     */
    public function importIncident(UsgsEarthquakeClient $client, UsgsEarthquakeMapper $mapper): void
    {
        $validated = $this->validate();

        try {
            $currentEvents = $mapper->map($client->fetchRecentEvents());
        } catch (ConnectionException|RequestException|\UnexpectedValueException $exception) {
            report($exception);
            $this->addError('selected_event_id', __('usgs.validation.catalogue_unavailable'));

            return;
        }

        $event = collect($currentEvents)->first(
            fn (array $event): bool => $event['id'] === $validated['selected_event_id'],
        );

        if ($event === null) {
            $this->addError('selected_event_id', __('usgs.validation.event_unavailable'));

            return;
        }

        $kaiju = Kaiju::query()->findOrFail((int) $validated['kaiju_id']);

        $incident = $kaiju->incidents()->create([
            'title' => $event['title'],
            'description' => 'Imported from USGS: '.$event['title'],
            'location' => $event['location'] ?? 'Unknown location',
            'status' => IncidentStatus::Open,
            'occurred_at' => CarbonImmutable::parse($event['occurred_at_iso'], 'UTC'),
            'source' => 'USGS',
            'external_event_id' => $event['id'],
            'external_url' => $event['url'],
            'magnitude' => $event['magnitude'],
            'latitude' => $event['latitude'],
            'longitude' => $event['longitude'],
            'depth' => $event['depth'],
        ]);

        $this->redirectRoute('incidents.show', $incident, navigate: true);
    }
}; ?>

<section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <header class="space-y-2">
        <flux:heading size="xl">{{ __('usgs.title') }}</flux:heading>
        <flux:text>{{ __('usgs.description') }}</flux:text>
    </header>

    @if ($error !== null)
        <div class="rounded-xl border border-red-200 bg-red-50 px-6 py-5 dark:border-red-900 dark:bg-red-950/40" role="alert">
            <flux:heading size="lg">{{ __('usgs.errors.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ $error }}</flux:text>
        </div>
    @elseif (count($events) === 0)
        <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700">
            <flux:heading size="lg">{{ __('usgs.empty.heading') }}</flux:heading>
            <flux:text class="mt-2">{{ __('usgs.empty.description') }}</flux:text>
        </div>
    @else
        <form wire:submit="importIncident" class="space-y-6" novalidate>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($events as $event)
                    <label wire:key="usgs-event-{{ $event['id'] }}" class="flex cursor-pointer flex-col gap-4 rounded-xl border border-sky-200 border-t-4 border-t-sky-500 bg-white p-5 shadow-sm has-[:checked]:ring-2 has-[:checked]:ring-sky-500 dark:border-sky-900 dark:border-t-sky-500 dark:bg-zinc-900">
                        <input wire:model="selected_event_id" type="radio" name="selected_event_id" value="{{ $event['id'] }}" class="sr-only">
                        <div class="space-y-1">
                            <p class="text-xs font-semibold tracking-wider text-sky-700 uppercase dark:text-sky-300">
                                {{ __('usgs.event_label') }}
                            </p>
                            <flux:heading size="lg">{{ $event['title'] }}</flux:heading>
                        </div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.magnitude') }}</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">{{ $event['magnitude'] ?? __('usgs.not_available') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('usgs.occurred_at') }}</dt>
                                <dd class="text-right text-zinc-900 dark:text-white">{{ $event['occurred_at'] }}</dd>
                            </div>
                        </dl>

                        @if ($event['location'] !== null)
                            <flux:text>{{ $event['location'] }}</flux:text>
                        @endif

                        @if ($event['url'] !== null)
                            <flux:button :href="$event['url']" target="_blank" variant="ghost" class="mt-auto self-start">
                                {{ __('usgs.view_source') }}
                            </flux:button>
                        @endif
                    </label>
                @endforeach
            </div>

            @error('selected_event_id')
                <flux:text class="text-red-600" role="alert">{{ $message }}</flux:text>
            @enderror

            <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 md:flex-row md:items-end">
                <flux:select wire:model="kaiju_id" :label="__('Kaiju')" required class="flex-1">
                    <flux:select.option value="">{{ __('Select a Kaiju') }}</flux:select.option>

                    @foreach ($this->kaijus as $kaiju)
                        <flux:select.option :value="$kaiju->id">
                            {{ $kaiju->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                <flux:button type="submit" variant="primary">
                    {{ __('Create incident') }}
                </flux:button>
            </div>

            @error('kaiju_id')
                <flux:text class="text-red-600" role="alert">{{ $message }}</flux:text>
            @enderror
        </form>
    @endif
</section>
